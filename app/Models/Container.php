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

#[ObservedBy(ContainerObserver::class)]
class Container extends Model
{
    protected $guarded = ['container_items', 'container_loading_addresses'];

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
}
