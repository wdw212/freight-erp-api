<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $invoiceTypeDetail = $this->invoice_type_display;
        $invoiceTypeName = $invoiceTypeDetail['name'] ?? '';
        $invoiceType = [
            'id' => $invoiceTypeDetail['id'],
            'name' => $invoiceTypeName,
            'type' => $this->invoiceType?->type ?? null,
            'tax_rate' => $this->invoiceType?->tax_rate ?? null,
            'remark' => $this->invoiceType?->remark ?? null,
        ];

        $data['invoice_type_id'] = $invoiceTypeDetail['id'];
        $data['invoice_type_name'] = $invoiceTypeName;
        $data['invoice_type'] = $invoiceType;
        $data['invoice_type_detail'] = $invoiceType;

        return $data;
    }
}
