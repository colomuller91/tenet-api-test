<?php

namespace App\Services\ConsumptionQuantity;

use App\Contracts\ConsumptionCalculationInterface;
use App\Models\ServiceConsumption;

class SpeechService implements ConsumptionCalculationInterface
{

    public function getQuantity(ServiceConsumption $consumption): float {
        return $consumption->quantity;
    }

    public function getPeriodText(ServiceConsumption $consumption): string {
        return "";
    }
}
