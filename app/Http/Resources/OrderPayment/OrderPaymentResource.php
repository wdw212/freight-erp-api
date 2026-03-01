<?php

namespace App\Http\Resources\OrderPayment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $company_header_id
 * @property mixed $company_header_name
 * @property mixed $no_invoice_remark
 * @property mixed $cny_amount
 * @property mixed $cny_invoice_number
 * @property mixed $usd_amount
 * @property mixed $usd_invoice_number
 * @property mixed $contact_person
 * @property mixed $contact_phone
 * @property mixed $remark
 * @property mixed $cny_is_cashed
 * @property mixed $usd_is_cashed
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
        $companyHeaderDetail = $this->company_header_display;
        $companyHeaderName = $companyHeaderDetail['name'] ?? '';

        return [
            'id' => $this->id,
            'company_header_id' => $companyHeaderDetail['id'],
            'company_header_name' => $companyHeaderName,
            'company_header' => $companyHeaderName,
            'company_header_detail' => $companyHeaderDetail,
            'no_invoice_remark' => $this->no_invoice_remark,
            'cny_amount' => $this->cny_amount,
            'cny_invoice_number' => $this->cny_invoice_number,
            'cny_is_cashed' => $this->cny_is_cashed,
            'usd_amount' => $this->usd_amount,
            'usd_invoice_number' => $this->usd_invoice_number,
            'usd_is_cashed' => $this->usd_is_cashed,
            'remark' => $this->remark,
        ];
    }
}
