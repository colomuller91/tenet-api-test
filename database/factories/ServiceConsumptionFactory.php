<?php

namespace Database\Factories;

use App\Enums\BillingType;
use App\Enums\ServiceIdentifier;
use App\Models\Service;
use App\Models\ServiceConsumption;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceConsumption>
 */
class ServiceConsumptionFactory extends Factory
{
    protected $model = ServiceConsumption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {

        $service = Service::all()->random();

        $start = null;
        $end = null;
        $quantity = 1;
        $createdAt = null;

        switch ($service->identifier) {
            case ServiceIdentifier::Backoffice: {
                $start = now()->subDays(rand(10, 30))->firstOfMonth()->startOfDay();
                $end = $start->clone()->lastOfMonth()->endOfDay();
                $createdAt = $start;
                break;
            }

            case ServiceIdentifier::Storage: {
                $start = now()->subDays(rand(10, 30));
                $end = $start->clone()->addDays(rand(1, 9));
                $quantity = rand(1, 1000);
                $createdAt = $start;
                break;
            }

            case ServiceIdentifier::Proxy: {
                $start = now()->subDays(rand(10, 30));
                $end = $start->clone()->addMinutes(rand(60, 1440));
                $createdAt = $start;
                break;
            }

            case ServiceIdentifier::Speech: {
                $quantity = rand(100, 999999999);
                $createdAt = now()->subDays(rand(10, 30));
                break;
            }
        }

        return [
            'service_id' => $service->id,
            'from_date' => $start,
            'to_date' => $end,
            'quantity' => $quantity,
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }

    public function configure(): Factory {
        return $this->afterCreating(function (ServiceConsumption $consumption){

            if (Service::where('identifier', ServiceIdentifier::Backoffice->value)->first()->id != $consumption->service_id) {
                return;
            }

            $shouldModify = true;
            $dateReference = $consumption->created_at->clone();

            while ($shouldModify) {
                $shouldModify = ServiceConsumption::where([
                    ['id', '<>', $consumption->id],
                    ['service_id', $consumption->service_id],
                    ['created_at', $dateReference],
                    ['customer_id', $consumption->customer_id],
                ])->exists();

                if ($shouldModify) {
                    $dateReference->subMonth();
                }
            }

            $consumption->from_date = $dateReference->clone()->firstOfMonth()->startOfDay();
            $consumption->to_date = $dateReference->clone()->firstOfMonth()->endOfDay();
            $consumption->created_at = $consumption->from_date;
            $consumption->save();


        });
    }
}
