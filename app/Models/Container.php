<?php
/**
 * 集装箱 Model
 */

namespace App\Models;

use App\Observers\ContainerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $order
 */
#[ObservedBy(ContainerObserver::class)]
class Container extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'no',
        'seal_number',
        'container_type_id',
        'serial_number',
        'pre_pull_wharf_id',
        'wharf_id',
        'drop_off_wharf_id',
        'is_entered_port',
        'driver',
        'fleet_id',
        'cargo_weight',
        'loading_at',
        'remark'
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    /**
     * 集装箱信息(件毛体)
     * @return HasMany
     */
    public function containerItems(): HasMany
    {
        return $this->hasMany(ContainerItem::class);
    }


    /**
     * @return HasMany
     */
    public function containerLoadingAddresses(): HasMany
    {
        return $this->hasMany(ContainerLoadingAddress::class);
    }
}
