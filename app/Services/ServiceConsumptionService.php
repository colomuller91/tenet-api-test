<?php

namespace App\Services;

use App\Enums\ServiceIdentifier;
use App\Models\Service;
use App\Models\ServiceConsumption;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use function PHPUnit\Framework\throwException;

class ServiceConsumptionService
{

    /**
     * @param $data
     * @return ServiceConsumption
     */
    public function createConsumption($data): ServiceConsumption {
        return ServiceConsumption::create($data);
    }

    /**
     * Check if backoffice service is billed for period parameters
     *
     * @param array $data
     * @param Service $service
     * @return array
     */
    public function validateConsumption(array $data, Service $service): array {
        $data['from_date'] = Carbon::parse($data['from_date']);
        $data['to_date'] = Carbon::parse($data['to_date']);
        $data['service_id'] = $service->id;

        if ($service->identifier == ServiceIdentifier::Backoffice) {
            $isStartOfMonth = $data['from_date']->equalTo($data['from_date']->clone()->firstOfMonth()->startOfDay());
            $isEndOfMonth = $data['to_date']->equalTo($data['to_date']->clone()->endOfMonth()->endOfDay()->setMicrosecond(0));

            if (!$isStartOfMonth || !$isEndOfMonth) {
                throw new BadRequestException('Dates must be start and end of the month for this service');
            }

            $existMonthlyConsumption = ServiceConsumption::where([
                'customer_id' => $data['customer_id'],
                'service_id' => $service->id,
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
            ])->exists();


            if ($existMonthlyConsumption) {
                throw new BadRequestException('Service consumption already exists for current period');
            }
        }

        return $data;
    }

    /**
     * @param ServiceConsumption $service_consumption
     * @return bool
     */
    public function checkInvoice(ServiceConsumption $service_consumption): bool {
        if ($service_consumption->invoiceLine()->exists() && $service_consumption->invoiceLine->invoice()->exists()){
            throw new BadRequestException('Service consumption can\'t be deleted, it\'s associated to an invoice line');
        }

        return true;
    }
}
