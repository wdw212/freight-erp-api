<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBillItem extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function orderBill(): BelongsTo
    {
        return $this->belongsTo(OrderBill::class);
    }

    /**
     * 费用类型展示名称（快照优先）
     * @return string
     */
    public function getFeeTypeDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->fee_type_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded('feeType')) {
            $relationName = trim((string)($this->feeType?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return '';
    }

    /**
     * 费用类型展示结构
     * @return array{id: int|null, name: string}
     */
    public function getFeeTypeDisplayAttribute(): array
    {
        return [
            'id' => empty($this->fee_type_id) ? null : (int)$this->fee_type_id,
            'name' => $this->fee_type_display_name,
        ];
    }
}
