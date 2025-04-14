<?php

namespace App\Http\Resources\OrderFile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $file
 * @property mixed $created_at
 * @property mixed $order
 */
class OrderFileResource extends JsonResource
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
            'business_user' => $this->order->businessUser,
            'company_header' => $this->order->orderDelegationHeader->companyHeader,
            'file' => $this->file,
            'url' => formatUrl($this->file),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
