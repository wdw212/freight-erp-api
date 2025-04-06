<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = [
        'order_payments'
    ];

    /**
     * 单据-应付款（一对多）
     * @return HasMany
     */
    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    /**
     * @return BelongsTo
     */
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    /**
     * 业务员
     * @return BelongsTo
     */
    public function businessUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'business_user_id');
    }
}
