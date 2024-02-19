<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    private float $total = 0;
    protected $fillable = [
        'number',
        'date',
        'customer_id'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d'
    ];

    protected $appends = [
        'total'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'customer_id'
    ];

    /**
     * @return HasMany
     */
    public function lines(): HasMany {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Returns invoice sub-totals sum
     * @return float
     */
    public function getTotalAttribute(): float {
        $this->lines()->each(fn($item) => $this->total+= $item->total_line);

        return round($this->total, config('app.currency.decimals'));
    }
}
