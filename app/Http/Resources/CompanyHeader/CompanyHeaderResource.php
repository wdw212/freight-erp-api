<?php

namespace App\Http\Resources\CompanyHeader;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $adminUser
 * @property mixed $companyType
 * @property mixed $company_name
 * @property mixed $tax_number
 * @property mixed $billing_address
 * @property mixed $company_phone
 * @property mixed $bank_name
 * @property mixed $bank_account
 * @property mixed $delivery_phone
 * @property mixed $delivery_email
 * @property mixed $contact_person
 * @property mixed $contact_phone
 * @property mixed $qq
 * @property mixed $distinction
 * @property mixed $delivery_address
 * @property mixed $remark
 * @property mixed $created_at
 */
class CompanyHeaderResource extends JsonResource
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
            'admin_user' => $this->adminUser,
            'company_type' => $this->companyType,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'billing_address' => $this->billing_address,
            'company_phone' => $this->company_phone,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'delivery_phone' => $this->delivery_phone,
            'delivery_email' => $this->delivery_email,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'qq' => $this->qq,
            'distinction' => $this->distinction,
            'delivery_address' => $this->delivery_address,
            'remark' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
