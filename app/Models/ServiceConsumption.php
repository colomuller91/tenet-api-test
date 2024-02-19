<?php

namespace App\Models;

use App\Contracts\ConsumptionCalculationInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceConsumption extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'customer_id',
        'from_date',
        'to_date',
        'quantity'
    ];

    protected $casts = [
        'from_date' => 'date:Y-m-d H:i:s',
        'to_date' => 'date:Y-m-d H:i:s'
    ];

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function invoiceLine() {
        return $this->hasOne(InvoiceLine::class, 'service_consumption_id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function getQuantity() {
        /** @var ConsumptionCalculationInterface $calculationService */
        $calculationService = $this->service->getCalculationService();
        return $calculationService->getQuantity($this);
    }

    public function getUnitPrice() {
        return $this->service->unit_price;
    }
}
