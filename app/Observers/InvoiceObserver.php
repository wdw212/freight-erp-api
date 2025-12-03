<?php

namespace App\Observers;

use App\Models\CompanyHeader;
use App\Models\Invoice;
use App\Models\Seller;

class InvoiceObserver
{
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
