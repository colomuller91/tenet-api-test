<?php

namespace App\Http\Controllers;

use App\Enums\ServiceIdentifier;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceConsumption;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Service $service)
    {
        return $service->consumptions()->get()->load([
            'customer' => fn($q) => $q->withTrashed()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Service $service, Request $request)
    {
        $data = $request->validate([
            'from_date' => 'date|before:to_date',
            'to_date' => 'date|after:from_date',
            'customer_id' => [
                'exists:App\Models\Customer,id',
            ],
            'quantity' => 'numeric|gt:0'
        ]);

        $data['from_date'] = Carbon::parse($data['from_date']);
        $data['to_date'] = Carbon::parse($data['to_date']);
        $data['service_id'] = $service->id;

        if ($service->identifier == ServiceIdentifier::Backoffice) {
            $isStartOfMonth = $data['from_date']->equalTo($data['from_date']->clone()->firstOfMonth()->startOfDay());
            $isEndOfMonth = $data['to_date']->equalTo($data['to_date']->clone()->endOfMonth()->endOfDay()->setMicrosecond(0));

            if (!$isStartOfMonth || !$isEndOfMonth) {
                return response()->json(
                    ['message' => 'Dates must be start and end of the month for this service'],
                    400
                );
            }

            $existMonthlyConsumption = ServiceConsumption::where([
                'customer_id' => $data['customer_id'],
                'service_id' => $service->id,
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
            ])->exists();


            if ($existMonthlyConsumption) {
                return response()->json(
                    ['message' => 'Service consumption already exists for current period'],
                    400
                );
            }
        }

        return ServiceConsumption::create($data);
    }

    /**
     * @param Customer $customer
     */
    public function customerConsumptions(Customer $customer) {
        return ServiceConsumption::with('service')
            ->where('customer_id', $customer->id)
            ->get();
    }


    /**
     * Display the specified resource.
     */
    public function show(ServiceConsumption $service_consumption)
    {
        return $service_consumption->load('customer');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceConsumption $service_consumption)
    {
        if ($service_consumption->invoiceLine()->exists() && $service_consumption->invoiceLine->invoice()->exists()){
            return response()->json(
                ['message' => 'Service consumption can\'t be deleted, it\'s associated to an invoice line'],
                400
            );
        }

        $service_consumption->deleteOrFail();
        return response()->json(['message' => 'Service consumption deleted']);
    }
}
