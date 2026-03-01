<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $shipping_company_id
 * @property mixed $shipping_company_name
 * @property mixed $job_no
 * @property mixed $orderDelegationHeader
 * @property mixed $orderType
 * @property mixed $bl_no
 * @property mixed $destination_port
 * @property mixed $operateUser
 * @property mixed $bl_status
 * @property mixed $receipt_total_cny_amount
 * @property mixed $receipt_cny_cashed_status
 * @property mixed $receipt_total_usd_amount
 * @property mixed $receipt_usd_cashed_status
 * @property mixed $gross_profit
 * @property mixed $special_fee
 * @property mixed $cashed_status
 * @property mixed $order_files_count
 * @property mixed $created_at
 */
class BusinessOrderResource extends JsonResource
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
            'job_no' => $this->job_no,
            'shipping_company_id' => $shippingCompanyDetail['id'],
            'shipping_company_name' => $shippingCompanyName,
            'shipping_company' => $shippingCompanyName,
            'shipping_company_detail' => $shippingCompanyDetail,
            'order_delegation_header' => $this->orderDelegationHeader ? array_merge(
                $this->orderDelegationHeader->toArray(),
                ['company_header_name' => $this->orderDelegationHeader->company_header_display_name]
            ) : null,
            'order_type' => $this->orderType,
            'bl_no' => $this->bl_no,
            'destination_port' => $this->destination_port,
            'sailing_at' => !empty($this->sailing_at) ? Carbon::parse($this->sailing_at)->format('Y-m-d') : '',
            'arrival_at' => !empty($this->arrival_at) ? Carbon::parse($this->arrival_at)->format('Y-m-d') : '',
            'operate_user' => $this->operateUser,
            'bl_status' => $this->bl_status,
            'receipt_total_cny_amount' => $this->receipt_total_cny_amount,
            'receipt_cny_cashed_status' => $this->receipt_cny_cashed_status,
            'receipt_total_usd_amount' => $this->receipt_total_usd_amount,
            'receipt_usd_cashed_status' => $this->receipt_usd_cashed_status,
            'gross_profit' => $this->gross_profit,
            'special_fee' => $this->special_fee,
            'cashed_status' => $this->cashed_status,
            'order_remark' => $this->orderRemark->remark ?? null,
            'order_files_count' => $this->order_files_count,
            'invoice_date' => $this->invoice_date ?? '',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
