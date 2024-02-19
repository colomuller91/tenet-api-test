<?php

namespace App\Contracts;

use App\Models\ServiceConsumption;

interface ConsumptionCalculationInterface
{
    public function getQuantity(ServiceConsumption $consumption): float;
    public function getPeriodText(ServiceConsumption $consumption): string;
}
