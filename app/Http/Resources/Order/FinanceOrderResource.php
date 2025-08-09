<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOrderResource extends JsonResource
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
            'job_no' => $this->job_no,
            'order_type' => $this->orderType,
            'bl_no' => $this->bl_no,
            'operation_user' => $this->operationUser,
            'business_user' => $this->businessUser,
            'container_type' => $this->container_type,
        ];
    }
}
