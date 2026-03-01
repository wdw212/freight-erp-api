<?php
/**
 * 订单 - 委托抬头 Model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDelegationHeader extends Model
{
    protected $guarded = [];

    protected $casts = [
        'remark' => 'json',
    ];

    /**
     * 单据
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 销货单位
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * 公司抬头
     * @return BelongsTo
     */
    public function companyHeader(): BelongsTo
    {
        return $this->belongsTo(CompanyHeader::class);
    }

    /**
     * 公司抬头展示名称（快照优先）
     * 规则：快照名 > 已预加载关联名 > 懒加载兜底（用于历史数据快照为空的情况）
     * @return string
     */
    public function getCompanyHeaderDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->company_header_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded('companyHeader')) {
            $relationName = trim((string)($this->companyHeader?->company_name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        if (!empty($this->company_header_id)) {
            $resolvedName = trim((string)(CompanyHeader::query()->find($this->company_header_id)?->company_name ?? ''));
            if ($resolvedName !== '') {
                return $resolvedName;
            }
        }

        return '';
    }

    /**
     * 公司抬头展示结构
     * @return array{id: int|null, name: string}
     */
    public function getCompanyHeaderDisplayAttribute(): array
    {
        return [
            'id' => empty($this->company_header_id) ? null : (int)$this->company_header_id,
            'name' => $this->company_header_display_name,
        ];
    }
}
