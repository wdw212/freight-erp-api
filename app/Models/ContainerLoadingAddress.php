<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContainerLoadingAddress extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * @return BelongsTo
     */
    public function loadingAddress(): BelongsTo
    {
        return $this->belongsTo(LoadingAddress::class);
    }
}
