<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    /**
     * @var array|string[]
     */
    public static array $modelType = [
        Role::class => '角色',
    ];
}
