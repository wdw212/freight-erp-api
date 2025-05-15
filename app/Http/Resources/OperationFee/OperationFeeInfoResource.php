<?php

namespace App\Http\Resources\OperationFee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $profit_adjustment_amount
 * @property mixed $remark
 * @property mixed $operationFeeItems
 * @property mixed $month_code
 */
class OperationFeeInfoResource extends JsonResource
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
            'month_code' => $this->month_code,
            'profit_adjustment_amount' => $this->profit_adjustment_amount,
            'remark' => $this->remark,
            'items' => $this->operationFeeItems
        ];
    }
}
