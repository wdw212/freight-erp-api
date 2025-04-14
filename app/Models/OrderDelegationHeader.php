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
}
