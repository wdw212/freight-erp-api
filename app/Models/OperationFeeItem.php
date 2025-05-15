<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationFeeItem extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function operationFee(): BelongsTo
    {
        return $this->belongsTo(OperationFee::class);
    }

    /**
     * @return BelongsTo
     */
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }
}
