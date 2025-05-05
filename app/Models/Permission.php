<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id')->with('children');
    }
}
