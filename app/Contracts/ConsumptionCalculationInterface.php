<?php

namespace App\Contracts;

use App\Models\ServiceConsumption;

interface ConsumptionCalculationInterface
{
    /**
     * @param ServiceConsumption $consumption
     * @return float
     */
    public function getQuantity(ServiceConsumption $consumption): float;

    /**
     * @param ServiceConsumption $consumption
     * @return string
     */
    public function getPeriodText(ServiceConsumption $consumption): string;
}
