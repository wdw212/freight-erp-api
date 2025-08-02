<?php

namespace App\Http\Resources\OrderReceipt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReceiptInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
