<?php

namespace App\Models;

use App\Observers\OrderBlInfoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(OrderBlInfoObserver::class)]
class OrderBlInfo extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'sender' => 'json',
        'receiver' => 'json',
        'notifier' => 'json',
    ];

    /**
     * 关联单据
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
