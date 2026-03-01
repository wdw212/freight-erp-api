<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $shipping_company_id
 * @property mixed $shipping_company_name
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
 * @property mixed $created_at
 * @property mixed $orderType
 * @property mixed $businessUser
 * @property mixed $operateUser
 * @property mixed $orderDelegationHeader
 * @property mixed $documentUser
 * @property mixed $commerceUser
 * @property mixed $orderRemark
 * @property mixed $payment_status
 * @property mixed $orderFilesCount
 * @property mixed $is_claimed
 * @property mixed $order_files_count
 * @property mixed $origin_harbor
 * @property mixed $destination_harbor
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shippingCompanyDetail = $this->shipping_company_display;
        $shippingCompanyName = $shippingCompanyDetail['name'] ?? '';

        return [
            'id' => $this->id,
            'order_type' => $this->orderType,
            'shipping_company_id' => $shippingCompanyDetail['id'],
            'shipping_company_name' => $shippingCompanyName,
            'shipping_company' => $shippingCompanyName,
            'shipping_company_detail' => $shippingCompanyDetail,
            'business_user' => $this->businessUser,
            'operate_user' => $this->operateUser,
            'document_user' => $this->documentUser,
            'commerce_user' => $this->commerceUser,
            'orderDelegationHeader' => $this->orderDelegationHeader ? array_merge(
                $this->orderDelegationHeader->toArray(),
                ['company_header_name' => $this->orderDelegationHeader->company_header_display_name]
            ) : null,
            'job_no' => $this->job_no,
            'contract_no' => $this->contract_no,
            'bl_no' => $this->bl_no,
            'origin_port' => $this->origin_port,
            'destination_port' => $this->destination_port,
            'origin_harbor' => $this->origin_harbor_display,
            'destination_harbor' => $this->destination_harbor_display,
            'ship_name' => $this->ship_name,
            'ship_no' => $this->ship_no,
            'container_type' => $this->container_type,
            'payment_method' => $this->payment_method,
            'cutoff_status' => $this->cutoff_status,
            'sailing_schedule' => $this->sailing_schedule,
            'bl_status' => $this->bl_status,
            'is_delivery' => $this->is_delivery,
            'sailing_at' => !empty($this->sailing_at) ? Carbon::parse($this->sailing_at)->format('Y-m-d') : '',
            'arrival_at' => !empty($this->arrival_at) ? Carbon::parse($this->arrival_at)->format('Y-m-d') : '',
            'finish_at' => $this->finish_at,
            'order_remark' => $this->orderRemark->remark ?? null,
            'payment_status' => $this->payment_status,
            'order_files_count' => $this->order_files_count,
            'is_claimed' => $this->is_claimed,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
