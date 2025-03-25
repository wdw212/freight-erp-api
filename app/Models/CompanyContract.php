<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyContract extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * @return BelongsTo
     */
    public function companyHeader(): BelongsTo
    {
        return $this->belongsTo(CompanyHeader::class);
    }
}
