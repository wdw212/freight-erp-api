<?php

namespace App\Observers;

use App\Models\CompanyHeader;
use App\Models\Invoice;
use App\Models\OrderReceipt;
use App\Models\Seller;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        Log::info('--发票创建成功--');

        $orderReceipt = new OrderReceipt();
        $orderReceipt->order_id = $invoice->order_id;
        $orderReceipt->company_header_id = $invoice->purchase_entity_id;
        $orderReceipt->cny_amount = $invoice->total_cny_amount;
        $orderReceipt->usd_amount = $invoice->total_usd_amount;
        $orderReceipt->save();
        Log::info('--应收款创建成功--');
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    public function saving(Invoice $invoice): void
    {
        // 处理销售方
        $saleEntity = Seller::query()->where('id', $invoice->sale_entity_id)->first();
        $invoice->sale_entity = [
            'name' => $saleEntity->name,
            'usc_code' => $invoice->sale_usc_code,
        ];
        // 处理购买方
        $purchaseEntity = CompanyHeader::query()->where('id', $invoice->purchase_entity_id)->first();
        $invoice->purchase_entity = [
            'name' => $purchaseEntity->company_name,
            'usc_code' => $invoice->purchase_usc_code,
        ];

        // 计算税额
        $invoice->tax_amount = calculateTaxAmount($invoice->total_cny_amount, $invoice->tax_rate);
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        // 删除关联发票详情
        $invoice->invoiceItems()->delete();
    }
}
