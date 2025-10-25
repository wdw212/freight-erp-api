<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float|int|mixed $cny_amount
 * @property float|int|mixed $usd_amount
 */
class OrderBill extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = [
        'order_bill_items'
    ];

    /**
     * 关联单据
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 账单详情
     * @return HasMany
     */
    public function orderBillItems(): HasMany
    {
        return $this->hasMany(OrderBillItem::class);
    }
}
