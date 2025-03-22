<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeTag extends Model
{
    public $guarded = [];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }
}
