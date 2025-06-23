<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 */
class PageAnnotation extends Model
{
    /**
     * 模型类型
     * @var array|string[]
     */
    public static array $modelTypeMap = [
        LoadingAddress::class => '装柜信息',
        CompanyHeader::class => '公司抬头',
        SftRecord::class => '收发通'
    ];
    public $timestamps = false;
    protected $guarded = [];
}
