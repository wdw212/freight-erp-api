<?php

namespace App\Models;

use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $order_id
 * @property mixed $invoice_type_id
 * @property mixed $email
 * @property mixed $remark
 * @property mixed $invoice_date
 * @property mixed $tax_rate
 * @property mixed $tax_amount
 * @property mixed $cny_invoice_no
 * @property mixed $usd_invoice_no
 * @property mixed $cny_remark
 * @property mixed $usd_remark
 * @property mixed $purchase_entity_id
 * @property mixed $purchase_usc_code
 * @property mixed $sale_entity_id
 * @property mixed $sale_usc_code
 */
#[ObservedBy(InvoiceObserver::class)]
class Invoice extends Model
{
    protected $guarded = [];

    /**
     * 关联订单
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 发票类型
     * @return BelongsTo
     */
    public function invoiceType(): BelongsTo
    {
        return $this->belongsTo(InvoiceType::class);
    }

    /**
     * 发票详情
     * @return HasMany
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * 人民币发票详情
     * @return HasMany
     */
    public function cnyInvoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->where('currency', 'cny');
    }

    /**
     * 美金发票详情
     * @return HasMany
     */
    public function usdInvoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->where('currency', 'usd');
    }
}
