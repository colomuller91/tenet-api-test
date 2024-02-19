<?php

namespace App\Services\ConsumptionQuantity;

use App\Contracts\ConsumptionCalculationInterface;
use App\Models\ServiceConsumption;

class StorageService implements ConsumptionCalculationInterface
{

    public function getQuantity(ServiceConsumption $consumption): float {
        return $consumption->quantity * ($consumption->from_date->diffInDays($consumption->to_date) + 1);
    }

    public function getPeriodText(ServiceConsumption $consumption): string {
        return "from {$consumption->from_date->format('Y-m-d')} to {$consumption->to_date->format('Y-m-d')}";
    }
}
