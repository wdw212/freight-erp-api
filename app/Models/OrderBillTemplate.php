<?php
/**
 * 单据账单-模版 Model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderBillTemplate extends Model
{
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'order_bill_items' => 'json'
    ];

    /**
     * 关联账号
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
