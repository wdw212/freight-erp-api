<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $admin_user_id
 * @property mixed $id
 */
class LoadingAddress extends Model
{
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'business_user_ids' => 'json',
        'operation_user_ids' => 'json',
        'document_user_ids' => 'json',
    ];

    /**
     * 省份
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
