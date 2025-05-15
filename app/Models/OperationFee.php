<?php
/**
 * 操作费模型
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationFee extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'month_code',
        'profit_adjustment_amount',
        'remark',
    ];

    /**
     * @return HasMany
     */
    public function operationFeeItems(): HasMany
    {
        return $this->hasMany(OperationFeeItem::class);
    }
}

