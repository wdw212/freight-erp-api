<?php

namespace App\Http\Resources\OrderBill;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $delegation_header
 * @property mixed $job_no
 * @property mixed $contract_no
 * @property mixed $bl_no
 * @property mixed $origin_port
 * @property mixed $destination_port
 * @property mixed $ship_name
 * @property mixed $ship_no
 * @property mixed $remark
 * @property mixed $created_at
 */
class OrderBillInfoResource extends JsonResource
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
            'delegation_header' => $this->delegation_header,
            'job_no' => $this->job_no,
            'contract_no' => $this->contract_no,
            'bl_no' => $this->bl_no,
            'origin_port' => $this->origin_port,
            'destination_port' => $this->destination_port,
            'ship_name' => $this->ship_name,
            'ship_no' => $this->ship_no,
            'sailing_at' => !empty($this->sailing_at) ? Carbon::parse($this->sailing_at)->format('Y-m-d') : '',
            'arrival_at' => !empty($this->arrival_at) ? Carbon::parse($this->arrival_at)->format('Y-m-d') : '',
            'remark' => $this->remark,
        ];
    }
}
