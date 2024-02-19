<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'vat_id'
    ];

    /**
     * @return HasMany
     */
    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * @return HasMany
     */
    public function serviceConsumptions(): HasMany {
        return $this->hasMany(ServiceConsumption::class, 'customer_id');
    }

}
