<?php

namespace App\Http\Resources\UsdExchangeRate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $month_code
 * @property mixed $exchange_rate
 */
class UsdExchangeRateInfoResource extends JsonResource
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
            'exchange_rate' => $this->exchange_rate,
        ];
    }
}
