<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $invoiceType
 * @property mixed $email
 * @property mixed $remark
 * @property mixed $cny_invoice_no
 * @property mixed $usd_invoice_no
 * @property mixed $cny_remark
 * @property mixed $usd_remark
 * @property mixed $invoice_date
 * @property mixed $tax_rate
 * @property mixed $tax_amount
 * @property mixed $created_at
 * @property mixed $purchase_entity
 * @property mixed $order
 * @property mixed $confirm_at
 * @property mixed $total_cny_amount
 * @property mixed $total_usd_amount
 * @property mixed $sale_entity
 */
class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $invoiceTypeDetail = $this->invoice_type_display;
        $invoiceTypeName = $invoiceTypeDetail['name'] ?? '';
        $invoiceType = [
            'id' => $invoiceTypeDetail['id'],
            'name' => $invoiceTypeName,
            'type' => $this->invoiceType?->type ?? null,
            'tax_rate' => $this->invoiceType?->tax_rate ?? null,
            'remark' => $this->invoiceType?->remark ?? null,
        ];

        return [
            'id' => $this->id,
            'order' => $this->order,
            'purchase_entity' => $this->purchase_entity,
            'sale_entity' => $this->sale_entity,
            'invoice_type_id' => $invoiceTypeDetail['id'],
            'invoice_type_name' => $invoiceTypeName,
            'invoice_type' => $invoiceType,
            'invoice_type_detail' => $invoiceType,
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'total_cny_amount' => $this->total_cny_amount,
            'cny_invoice_no' => $this->cny_invoice_no,
            'total_usd_amount' => $this->total_usd_amount,
            'usd_invoice_no' => $this->usd_invoice_no,
            'invoice_date' => $this->invoice_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'confirm_at' => $this->confirm_at,
        ];
    }
}
