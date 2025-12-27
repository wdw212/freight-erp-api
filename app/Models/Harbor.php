<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $code 代码
 * @property mixed $name 港口
 * @property mixed $en_name 港口（英文）
 * @property mixed $en_country 国家（英文）
 * @property mixed $country 国家（中文）
 * @property mixed $route 航线
 * @property mixed $remark 备注
 * @property mixed $id Id
 */
class Harbor extends Model
{
    protected $guarded = [];
}
