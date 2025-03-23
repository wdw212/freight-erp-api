<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    /**
     * 关联上级
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    /**
     * 关联下级
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }
}
