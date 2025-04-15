<?php

namespace App\Http\Resources\ShippingCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $free_container_days
 * @property mixed $tracking_url
 * @property mixed $remark
 * @property mixed $created_at
 * @property mixed $name
 * @property mixed $phone
 */
class ShippingCompanyInfoResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'free_container_days' => $this->free_container_days,
            'tracking_url' => $this->tracking_url,
            'remark' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
