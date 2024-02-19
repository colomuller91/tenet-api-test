<?php

namespace App\Services;

use App\Enums\ServiceIdentifier;
use App\Models\InvoiceLine;
use App\Models\Service;
use App\Models\ServiceConsumption;

class InvoiceLineService
{
    /**
     * @param ServiceConsumption $consumption
     * @return string
     */
    private function getDetailDescription(ServiceConsumption $consumption) {

        /** @var Service $service */
        $service = $consumption->service;

        $description = $service->name . ' service';
        $forPeriodText = $service->getCalculationService()->getPeriodText($consumption);

        return trim("$description $forPeriodText");
    }

    /**
     * @param ServiceConsumption $consumption
     * @return InvoiceLine
     */
    public function create(ServiceConsumption $consumption): InvoiceLine {

        return new InvoiceLine([
            'description' => $this->getDetailDescription($consumption),
            'unit_price' => $consumption->getUnitPrice(),
            'quantity' => $consumption->getQuantity(),
            'service_consumption_id' => $consumption->id
        ]);
    }

}
