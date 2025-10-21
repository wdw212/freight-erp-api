<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessOrderResource extends JsonResource
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
            'order_delegation_header' => $this->orderDelegationHeader,
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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
