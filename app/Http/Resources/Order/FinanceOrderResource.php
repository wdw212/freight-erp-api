<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $job_no
 * @property mixed $orderType
 * @property mixed $bl_no
 * @property mixed $operationUser
 * @property mixed $businessUser
 * @property mixed $container_type
 * @property mixed $sailing_at
 * @property mixed $is_delivery
 * @property mixed $payment_total_cny_amount
 * @property mixed $payment_total_usd_amount
 * @property mixed $receipt_total_cny_amount
 * @property mixed $receipt_total_usd_amount
 * @property mixed $finish_at
 */
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
            'sailing_at' => $this->sailing_at,
            'is_delivery' => $this->is_delivery,
            'payment_total_cny_amount' => $this->payment_total_cny_amount,
            'payment_total_usd_amount' => $this->payment_total_usd_amount,
            'receipt_total_cny_amount' => $this->receipt_total_cny_amount,
            'receipt_total_usd_amount' => $this->receipt_total_usd_amount,
            'finish_at' => formatDate($this->finish_at),
        ];
    }
}
