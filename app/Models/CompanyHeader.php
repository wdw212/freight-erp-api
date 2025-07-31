<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyHeader extends Model
{
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'company_type' => 'json',
        'business_user_ids' => 'json',
        'operation_user_ids' => 'json',
        'document_user_ids' => 'json',
    ];

    /**
     * @var array|string[]
     */
    public static array $companyTypeMap = [
        0 => '委托抬头',
        1 => '应付抬头',
        2 => '应收抬头'
    ];

    /**
     * 关联公司类型
     * @return BelongsTo
     */
    public function companyType(): BelongsTo
    {
        return $this->belongsTo(CompanyType::class);
    }

    /**
     * 关联业务员
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
