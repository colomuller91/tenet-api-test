<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoiceLine extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_consumption_id',
        'quantity',
        'unit_price',
        'description'
    ];

    protected $appends = [
      'total_line'
    ];

    protected $hidden = [
        'service_consumption_id',
        'invoice_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * @return HasOne
     */
    public function serviceConsumption(): HasOne {
        return $this->hasOne(ServiceConsumption::class, 'service_consumption_id');
    }

    /**
     * Get invoice detail subtotal
     * @return float
     */
    public function getTotalLineAttribute() {
        return round($this->quantity * $this->unit_price, config('app.currency.decimals'));
    }
}
