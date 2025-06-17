<?php

namespace App\Http\Resources\LoadingAddress;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $province
 * @property mixed $city
 * @property mixed $district
 * @property mixed $address
 * @property mixed $contact_name
 * @property mixed $phone
 * @property mixed $remark
 * @property mixed $created_at
 * @property mixed $region
 * @property mixed $adminUser
 * @property mixed $freight
 * @property mixed $keyword
 */
class LoadingAddressResource extends JsonResource
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
            'region' => $this->region,
            'address' => $this->address,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'remark' => $this->remark,
            'freight' => $this->freight,
            'keyword' => $this->keyword,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
