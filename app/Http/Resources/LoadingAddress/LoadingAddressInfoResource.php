<?php

namespace App\Http\Resources\LoadingAddress;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $region_id
 * @property mixed $business_user_ids
 * @property mixed $operation_user_ids
 * @property mixed $document_user_ids
 * @property mixed $contact_name
 * @property mixed $phone
 * @property mixed $remark
 * @property mixed $freight
 * @property mixed $keyword
 * @property mixed $address
 */
class LoadingAddressInfoResource extends JsonResource
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
            'region_id' => $this->region_id,
            'business_user_ids' => $this->business_user_ids,
            'operation_user_ids' => $this->operation_user_ids,
            'document_user_ids' => $this->document_user_ids,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'freight' => $this->freight,
            'keyword' => $this->keyword,
            'remark' => $this->remark,
            'address' => $this->address,
        ];
    }
}
