<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'currency',
        'fee_type_id',
        'fee_type_name',
        'unit',
        'quantity',
        'amount',
    ];

    public $timestamps = false;

    /**
     * 关联发票
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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
