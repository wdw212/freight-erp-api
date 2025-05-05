<?php

namespace App\Http\Resources\LoadingAddress;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $region_id
 * @property mixed $business_user_id
 * @property mixed $operation_user_id
 * @property mixed $document_user_id
 * @property mixed $contact_name
 * @property mixed $phone
 * @property mixed $remark
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
            'business_user_id' => (int)$this->business_user_id,
            'operation_user_id' => (int)$this->operation_user_id,
            'document_user_id' => (int)$this->document_user_id,
            'contact_name' => (int)$this->contact_name,
            'phone' => $this->phone,
            'remark' => $this->remark,
        ];
    }
}
