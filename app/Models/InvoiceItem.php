<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rules\In;
use voku\helper\ASCII;

class InvoiceItem extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * 关联发票
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
