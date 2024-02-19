<?php

namespace App\Services\ConsumptionQuantity;

use App\Contracts\ConsumptionCalculationInterface;
use App\Models\ServiceConsumption;

class ProxyService implements ConsumptionCalculationInterface
{

    public function getQuantity(ServiceConsumption $consumption): float {
        return $consumption->from_date->diffInMinutes($consumption->to_date) + 1;
    }

    public function getPeriodText(ServiceConsumption $consumption): string {
        return "from {$consumption->from_date->format('Y-m-d H:i:s')} to {$consumption->to_date->format('Y-m-d H:i:s')}";
    }
}
