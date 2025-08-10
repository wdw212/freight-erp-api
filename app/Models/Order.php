<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int|mixed $is_delivery
 * @property mixed $id
 * @property mixed $payment_total_cny_amount
 * @property mixed $payment_total_usd_amount
 */
#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = [
        'order_payments',
    ];

    /**
     * 单据-应付款
     * @return HasMany
     */
    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    /**
     * 订单类型
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

    /**
     * 单据委托抬头
     * @return HasOne
     */
    public function orderDelegationHeader(): HasOne
    {
        return $this->hasOne(OrderDelegationHeader::class);
    }

    /**
     * 单据文件
     * @return HasMany
     */
    public function orderFiles(): HasMany
    {
        return $this->hasMany(OrderFile::class);
    }

    /**
     * 应收款
     * @return HasMany
     */
    public function orderReceipts(): HasMany
    {
        return $this->hasMany(OrderReceipt::class);
    }

    /**
     * @return HasMany
     */
    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }

    /**
     * 操作员
     * @return BelongsTo
     */
    public function operateUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'operate_user_id');
    }

    /**
     * 商务员
     * @return BelongsTo
     */
    public function commerceUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'commerce_user_id');
    }
}
