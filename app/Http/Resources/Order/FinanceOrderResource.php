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
 * @property mixed $payment_cny_cashed_status
 * @property mixed $payment_usd_cashed_status
 * @property mixed $receipt_cny_cashed_status
 * @property mixed $receipt_usd_cashed_status
 * @property mixed $cashed_status
 * @property mixed $invoice_status
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
            'payment_cny_cashed_status' => $this->payment_cny_cashed_status,
            'payment_total_usd_amount' => $this->payment_total_usd_amount,
            'payment_usd_cashed_status' => $this->payment_usd_cashed_status,
            'receipt_total_cny_amount' => $this->receipt_total_cny_amount,
            'receipt_cny_cashed_status' => $this->receipt_cny_cashed_status,
            'receipt_total_usd_amount' => $this->receipt_total_usd_amount,
            'receipt_usd_cashed_status' => $this->receipt_usd_cashed_status,
            'finish_at' => formatDate($this->finish_at),
            'cashed_status' => $this->cashed_status,
            'invoice_status' => $this->invoice_status,
        ];
    }
}
