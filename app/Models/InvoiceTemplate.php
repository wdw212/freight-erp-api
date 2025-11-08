<?php
/**
 * 发票模版
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cny_invoice_items' => 'json',
        'usd_invoice_items' => 'json',
    ];

    /**
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
