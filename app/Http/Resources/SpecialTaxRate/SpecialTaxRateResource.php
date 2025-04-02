<?php

namespace App\Http\Resources\SpecialTaxRate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $month_code
 * @property mixed $one_amount
 * @property mixed $one_tax_rate
 * @property mixed $one_handling_fee
 * @property mixed $two_tax_rate
 * @property mixed $two_amount
 * @property mixed $two_handling_fee
 */
class SpecialTaxRateResource extends JsonResource
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
            'one_amount' => $this->one_amount,
            'one_tax_rate' => $this->one_tax_rate,
            'one_handling_fee' => $this->one_handling_fee,
            'two_amount' => $this->two_amount,
            'two_tax_rate' => $this->two_tax_rate,
            'two_handling_fee' => $this->two_handling_fee,
        ];
    }
}
