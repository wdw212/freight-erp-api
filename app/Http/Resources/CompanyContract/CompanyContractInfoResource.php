<?php

namespace App\Http\Resources\CompanyContract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $no
 * @property mixed $company_header_id
 * @property mixed $seller_id
 * @property mixed $type
 * @property mixed $phone
 * @property mixed $start_at
 * @property mixed $expire_at
 * @property mixed $remark
 * @property mixed $created_at
 */
class CompanyContractInfoResource extends JsonResource
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
            'no' => $this->no,
            'company_header_id' => $this->company_header_id,
            'seller_id' => $this->seller_id,
            'type' => $this->type,
            'phone' => $this->phone,
            'start_at' => $this->start_at,
            'expire_at' => $this->expire_at,
            'remark' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
