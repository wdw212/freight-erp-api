<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $tax_number
 * @property mixed $address
 * @property mixed $phone
 * @property mixed $bank_name
 * @property mixed $bank_account
 * @property mixed $created_at
 * @property mixed $name
 * @property mixed $logo
 * @property mixed $remark
 */
class SellerResource extends JsonResource
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
            'logo' => formatUrl($this->logo),
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'address' => $this->address,
            'phone' => $this->phone,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'remark' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
