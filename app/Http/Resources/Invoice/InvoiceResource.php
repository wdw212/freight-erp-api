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
        return [
            'id' => $this->id,
            'order' => $this->order,
            'invoice_type' => $this->invoiceType,
            'email' => $this->email,
            'remark' => $this->remark,
            'cny_invoice_no' => $this->cny_invoice_no,
            'usd_invoice_no' => $this->usd_invoice_no,
            'cny_remark' => $this->cny_remark,
            'usd_remark' => $this->usd_remark,
            'invoice_date' => $this->invoice_date,
            'purchase_entity' => $this->purchase_entity,
            'sale_entity' => $this->sale_entity ?? '',
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
