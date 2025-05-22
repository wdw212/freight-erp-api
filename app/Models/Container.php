<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    protected $guarded = [];

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
