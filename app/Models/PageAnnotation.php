<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed|string $model_type
 * @property mixed $content
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
        SftRecord::class => '收发通',
        Region::class => '地区',
    ];

    /**
     * @var array|string[]
     */
    public static array $getModelType = [
        'loading_address' => LoadingAddress::class,
        'company_header' => CompanyHeader::class,
        'sft_record' => SftRecord::class,
        'region' => Region::class,
    ];

    public $timestamps = false;
    protected $guarded = [];
}
