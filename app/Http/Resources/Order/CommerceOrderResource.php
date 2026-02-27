<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $shipping_company_id
 * @property mixed $shipping_company_name
 * @property mixed $job_no
 * @property mixed $origin_port
 * @property mixed $destination_port
 * @property mixed $bl_no
 * @property mixed $container_type
 * @property mixed $sailing_schedule
 * @property mixed $sailing_at
 * @property mixed $arrival_at
 * @property mixed $finish_at
 * @property mixed $operationUser
 * @property mixed $businessUser
 * @property mixed $is_delivery
 * @property mixed $booking_info
 * @property mixed $orderRemark
 * @property mixed $is_claimed
 * @property mixed $operateUser
 */
class CommerceOrderResource extends JsonResource
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
            'origin_port' => $this->origin_port,
            'destination_port' => $this->destination_port,
            'shipping_company_id' => $this->shipping_company_id,
            'shipping_company_name' => $this->shipping_company_name,
            'shipping_company' => [
                'id' => $this->shipping_company_id,
                'name' => $this->shipping_company_name,
            ],
            'bl_no' => $this->bl_no,
            'container_type' => $this->container_type,
            'sailing_schedule' => $this->sailing_schedule,
            'sailing_at' => formatDate($this->sailing_at),
            'arrival_at' => formatDate($this->arrival_at),
            'finish_at' => $this->finish_at,
            'operate_user' => $this->operateUser,
            'business_user' => $this->businessUser,
            'is_delivery' => $this->is_delivery,
            'booking_info' => $this->booking_info,
            'order_remark' => $this->orderRemark->remark ?? null,
            'is_claimed' => $this->is_claimed,
        ];
    }
}
