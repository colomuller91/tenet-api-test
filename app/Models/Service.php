<?php

namespace App\Models;

use App\Contracts\ConsumptionCalculationInterface;
use App\Enums\ServiceIdentifier;
use App\Services\ConsumptionQuantity\BackofficeService;
use App\Services\ConsumptionQuantity\ProxyService;
use App\Services\ConsumptionQuantity\SpeechService;
use App\Services\ConsumptionQuantity\StorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'memo',
        'unit_price'
    ];

    protected $casts = [
        'identifier' => ServiceIdentifier::class
    ];

    /**
     * @return HasMany
     */
    public function consumptions(): HasMany {
        return $this->hasMany(ServiceConsumption::class, 'service_id');
    }


    /**
     * Matches by identifier which service must be used to calculate billing
     * @return ConsumptionCalculationInterface
     */
    public function getCalculationService(): ConsumptionCalculationInterface {
        $serviceClasss = match ($this->identifier) {
            ServiceIdentifier::Backoffice => BackofficeService::class,
            ServiceIdentifier::Storage => StorageService::class,
            ServiceIdentifier::Proxy => ProxyService::class,
            ServiceIdentifier::Speech => SpeechService::class
        };

        return app($serviceClasss);
    }
}
