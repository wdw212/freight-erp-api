<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int|mixed $is_delivery
 * @property mixed $id
 * @property mixed $payment_total_cny_amount
 * @property mixed $payment_total_usd_amount
 * @property int|mixed $is_claimed
 * @property mixed $orderBlInfo
 * @property int|mixed $payment_status
 * @property Carbon|mixed $finish_at
 * @property mixed $origin_harbor
 * @property mixed $destination_harbor
 * @property mixed $originHarbor
 * @property mixed $destinationHarbor
 * @property int|mixed $is_finish
 * @property int|mixed $receipt_total_cny_amount
 * @property int|mixed $receipt_total_usd_amount
 * @property mixed $gross_profit_cny
 * @property mixed $usd_exchange_rate
 * @property mixed|string $gross_profit_usd
 * @property mixed|string $total_profit
 * @property mixed $special_fee
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
     * @var string[]
     */
    protected $casts = [
        'booking_info' => 'json'
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

    /**
     * 单证员
     * @return BelongsTo
     */
    public function documentUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'document_user_id');
    }

    /**
     * 订单备注
     * @return HasOne
     */
    public function orderRemark(): HasOne
    {
        return $this->hasOne(OrderRemark::class);
    }

    /**
     * 单据信息
     * @return HasOne
     */
    public function orderBlInfo(): HasOne
    {
        return $this->hasOne(OrderBlInfo::class, 'order_id', 'id');
    }

    /**
     * 始发港
     * @return BelongsTo
     */
    public function originHarbor(): BelongsTo
    {
        return $this->belongsTo(Harbor::class, 'origin_harbor_id');
    }

    /**
     * 目的港
     * @return BelongsTo
     */
    public function destinationHarbor(): BelongsTo
    {
        return $this->belongsTo(Harbor::class, 'destination_harbor_id');
    }

    /**
     * 进港码头
     * @return BelongsTo
     */
    public function enteredPortWharf(): BelongsTo
    {
        return $this->belongsTo(Wharf::class, 'entered_port_wharf_id');
    }

    /**
     * 船公司
     * @return BelongsTo
     */
    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id');
    }

    /**
     * 统一船公司展示名称（快照优先）
     * 规则：快照名 > 关联名（已预加载时）> ID字符串
     * @return string
     */
    public function getShippingCompanyDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->shipping_company_name ?? ''));
        $shippingCompanyId = empty($this->shipping_company_id) ? null : (int)$this->shipping_company_id;
        $snapshotLooksLikeId = $snapshotName !== ''
            && ctype_digit($snapshotName)
            && !empty($shippingCompanyId)
            && (int)$snapshotName === $shippingCompanyId;

        if ($snapshotName !== '' && !$snapshotLooksLikeId) {
            return $snapshotName;
        }

        if ($this->relationLoaded('shippingCompany')) {
            $relationName = trim((string)($this->shippingCompany?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        // 兜底查询：避免关系未预加载或历史快照异常时前端回退显示为ID
        if (!empty($shippingCompanyId)) {
            $resolvedName = trim((string)(ShippingCompany::query()->find($shippingCompanyId)?->name ?? ''));
            if ($resolvedName !== '') {
                return $resolvedName;
            }
        }

        return '';
    }

    /**
     * 统一船公司展示结构
     * @return array{id: int|null, name: string}
     */
    public function getShippingCompanyDisplayAttribute(): array
    {
        return [
            'id' => empty($this->shipping_company_id) ? null : (int)$this->shipping_company_id,
            'name' => $this->shipping_company_display_name,
        ];
    }

    /**
     * 始发港展示名称（快照优先）
     * 兼容旧数据（JSON 对象）和新数据（纯字符串名称）
     * @return string
     */
    public function getOriginHarborDisplayNameAttribute(): string
    {
        return $this->resolveHarborSnapshotName($this->origin_harbor, $this->origin_harbor_id, 'originHarbor');
    }

    /**
     * 始发港展示结构
     * @return array{id: int|null, name: string}
     */
    public function getOriginHarborDisplayAttribute(): array
    {
        return [
            'id' => empty($this->origin_harbor_id) ? null : (int)$this->origin_harbor_id,
            'name' => $this->origin_harbor_display_name,
        ];
    }

    /**
     * 目的港展示名称（快照优先）
     * @return string
     */
    public function getDestinationHarborDisplayNameAttribute(): string
    {
        return $this->resolveHarborSnapshotName($this->destination_harbor, $this->destination_harbor_id, 'destinationHarbor');
    }

    /**
     * 目的港展示结构
     * @return array{id: int|null, name: string}
     */
    public function getDestinationHarborDisplayAttribute(): array
    {
        return [
            'id' => empty($this->destination_harbor_id) ? null : (int)$this->destination_harbor_id,
            'name' => $this->destination_harbor_display_name,
        ];
    }

    /**
     * 解析港口快照名称（兼容旧 JSON 格式）
     * @param mixed $snapshot
     * @param mixed $harborId
     * @param string $relation
     * @return string
     */
    private function resolveHarborSnapshotName(mixed $snapshot, mixed $harborId, string $relation): string
    {
        $raw = trim((string)($snapshot ?? ''));
        if ($raw !== '') {
            // 兼容旧格式：快照为 JSON 字符串（整个 Harbor 模型）
            if (str_starts_with($raw, '{')) {
                $decoded = json_decode($raw, true);
                $name = trim((string)($decoded['name'] ?? ''));
                if ($name !== '') {
                    return $name;
                }
            } else {
                return $raw;
            }
        }

        if ($this->relationLoaded($relation)) {
            $relationName = trim((string)($this->{$relation}?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return '';
    }

    /**
     * 进港码头展示名称（快照优先）
     * @return string
     */
    public function getEnteredPortWharfDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->entered_port_wharf_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded('enteredPortWharf')) {
            $relationName = trim((string)($this->enteredPortWharf?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return empty($this->entered_port_wharf_id) ? '' : (string)$this->entered_port_wharf_id;
    }

    /**
     * 进港码头展示结构
     * @return array{id: int|null, name: string}
     */
    public function getEnteredPortWharfDisplayAttribute(): array
    {
        return [
            'id' => empty($this->entered_port_wharf_id) ? null : (int)$this->entered_port_wharf_id,
            'name' => $this->entered_port_wharf_display_name,
        ];
    }
}
