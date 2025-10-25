<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBillContainer extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * 订单账单
     * @return BelongsTo
     */
    public function orderBill(): BelongsTo
    {
        return $this->belongsTo(OrderBill::class);
    }
}
