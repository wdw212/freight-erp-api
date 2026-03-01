<?php
/**
 * 单据-应付款
 */

namespace App\Models;

use App\Observers\OrderPaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|mixed $is_finish
 */
#[ObservedBy(OrderPaymentObserver::class)]
class OrderPayment extends Model
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
    public function companyHeader(): BelongsTo
    {
        return $this->belongsTo(CompanyHeader::class, 'company_header_id');
    }

    /**
     * 公司抬头展示名称（快照优先）
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

        return empty($this->company_header_id) ? '' : (string)$this->company_header_id;
    }

    /**
     * 公司抬头展示结构
     * @return array{id:int|null,name:string}
     */
    public function getCompanyHeaderDisplayAttribute(): array
    {
        return [
            'id' => empty($this->company_header_id) ? null : (int)$this->company_header_id,
            'name' => $this->company_header_display_name,
        ];
    }
}
