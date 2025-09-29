<?php

namespace App\Models;

use App\Observers\OrderFileObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $file
 */
#[ObservedBy(OrderFileObserver::class)]
class OrderFile extends Model
{
    protected $guarded = [
        'url'
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
