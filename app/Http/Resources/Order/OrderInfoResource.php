<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $shipping_company_id
 * @property mixed $order_type_id
 * @property mixed $business_user_id
 * @property mixed $operation_user_id
 * @property mixed $document_user_id
 * @property mixed $commerce_user_id
 * @property mixed $job_no
 * @property mixed $contract_no
 * @property mixed $bl_no
 * @property mixed $origin_port
 * @property mixed $destination_port
 * @property mixed $ship_name
 * @property mixed $ship_no
 * @property mixed $container_type
 * @property mixed $payment_method
 * @property mixed $cutoff_status
 * @property mixed $sailing_schedule
 * @property mixed $bl_status
 * @property mixed $is_delivery
 * @property mixed $sailing_at
 * @property mixed $finish_at
 * @property mixed $arrival_at
 */
class OrderInfoResource extends JsonResource
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
            'order_type_id' => $this->order_type_id,
            'shipping_company_id' => $this->shipping_company_id,
            'business_user_id' => $this->business_user_id,
            'operation_user_id' => $this->operation_user_id,
            'document_user_id' => $this->document_user_id,
            'commerce_user_id' => $this->commerce_user_id,
            'job_no' => $this->job_no,
            'contract_no' => $this->contract_no,
            'bl_no' => $this->bl_no,
            'origin_port' => $this->origin_port,
            'destination_port' => $this->destination_port,
            'ship_name' => $this->ship_name,
            'ship_no' => $this->ship_no,
            'container_type' => $this->container_type,
            'payment_method' => $this->payment_method,
            'cutoff_status' => $this->cutoff_status,
            'sailing_schedule' => $this->sailing_schedule,
            'bl_status' => $this->bl_status,
            'is_delivery' => $this->is_delivery,
            'sailing_at' => $this->sailing_at,
            'arrival_at' => $this->arrival_at,
            'finish_at' => $this->finish_at,
        ];
    }
}
