<?php

namespace App\Http\Resources\OrderPayment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $company_header_id
 * @property mixed $no_invoice_remark
 * @property mixed $cny_amount
 * @property mixed $cny_invoice_number
 * @property mixed $usd_amount
 * @property mixed $usd_invoice_number
 * @property mixed $contact_person
 * @property mixed $contact_phone
 * @property mixed $remark
 */
class OrderPaymentResource extends JsonResource
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
            'company_header_id' => $this->company_header_id,
            'no_invoice_remark' => $this->no_invoice_remark,
            'cny_amount' => $this->cny_amount,
            'cny_invoice_number' => $this->cny_invoice_number,
            'usd_amount' => $this->usd_amount,
            'usd_invoice_number' => $this->usd_invoice_number,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'remark' => $this->remark,
        ];
    }
}
