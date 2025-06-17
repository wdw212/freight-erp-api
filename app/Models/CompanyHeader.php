<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyHeader extends Model
{
    protected $guarded = [];

    protected $casts = [
        'company_type' => 'json'
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
