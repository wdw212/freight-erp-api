<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 */
class SftRecord extends Model
{
    public $guarded = [];

    protected $casts = [
        'operation_user_ids' => 'json',
        'document_user_ids' => 'json',
        'commerce_user_ids' => 'json',
    ];

    /**
     * @return BelongsTo
     */
    public function confirmUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'confirm_user_id', 'id');
    }
}
