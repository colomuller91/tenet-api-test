<?php

namespace App\Models;

use App\Contracts\ConsumptionCalculationInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $hidden = [
        'service_id',
        'customer_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'from_date' => 'date:Y-m-d H:i:s',
        'to_date' => 'date:Y-m-d H:i:s'
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return HasOne
     */
    public function invoiceLine(): HasOne {
        return $this->hasOne(InvoiceLine::class, 'service_consumption_id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    /**
     * @return float
     */
    public function getQuantity(): float {
        /** @var ConsumptionCalculationInterface $calculationService */
        $calculationService = $this->service->getCalculationService();
        return $calculationService->getQuantity($this);
    }

    /**
     * @return float
     */
    public function getUnitPrice():float {
        return $this->service->unit_price;
    }
}
