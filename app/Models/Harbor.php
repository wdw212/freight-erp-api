<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $code
 * @property mixed $name
 * @property mixed $en_name
 * @property mixed $en_country
 * @property mixed $country
 * @property mixed $route
 * @property mixed $remark
 */
class Harbor extends Model
{
    protected $guarded = [];
}
