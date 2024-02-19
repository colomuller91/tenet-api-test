<?php

namespace App\Services\ConsumptionQuantity;

use App\Contracts\ConsumptionCalculationInterface;
use App\Models\ServiceConsumption;
use Carbon\Carbon;

class BackofficeService implements ConsumptionCalculationInterface
{

    public function getQuantity(ServiceConsumption $consumption): float {
        //monthly billed
        return $consumption->from_date->diffInMonths($consumption->to_date) + 1;
    }

    public function getPeriodText(ServiceConsumption $consumption): string {
        return "from {$consumption->from_date->format('Y-m-d')} to {$consumption->to_date->format('Y-m-d')}";
    }
}
