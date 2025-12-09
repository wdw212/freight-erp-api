<?php
/**
 * 单据-应收款
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $order_id
 * @property mixed $company_header_id
 * @property mixed $cny_amount
 * @property mixed $usd_amount
 */
class OrderReceipt extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
