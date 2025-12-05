<?php

namespace App\Http\Resources\InvoiceTemplate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $remark
 * @property mixed $cny_remark
 * @property mixed $usd_remark
 * @property mixed $cny_invoice_items
 * @property mixed $usd_invoice_items
 * @property mixed $purchase_entity_id
 * @property mixed $purchase_usc_code
 * @property mixed $invoice_type_id
 */
class InvoiceTemplateInfoResource extends JsonResource
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
            'invoice_type_id' => $this->invoice_type_id,
            'name' => $this->name,
            'email' => $this->email,
            'remark' => $this->remark,
            'cny_remark' => $this->cny_remark,
            'usd_remark' => $this->usd_remark,
            'cny_invoice_items' => $this->cny_invoice_items,
            'usd_invoice_items' => $this->usd_invoice_items,
            'purchase_entity_id' => $this->purchase_entity_id,
            'purchase_usc_code' => $this->purchase_usc_code
        ];
    }
}
