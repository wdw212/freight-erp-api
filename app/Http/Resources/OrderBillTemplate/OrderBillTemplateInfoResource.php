<?php

namespace App\Http\Resources\OrderBillTemplate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderBillTemplateInfoResource extends JsonResource
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
            'order_bill_items' => $this->order_bill_items,
            'cost_share' => $this->cost_share,
            'customer_payment_info' => $this->customer_payment_info,
            'company_receipt_info' => $this->company_receipt_info,
        ];
    }
}
