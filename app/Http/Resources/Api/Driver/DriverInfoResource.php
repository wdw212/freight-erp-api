<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $plate_number
 * @property mixed $name
 * @property mixed $phone
 * @property mixed $remark
 */
class DriverInfoResource extends JsonResource
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
            'plate_number' => $this->plate_number,
            'name' => $this->name,
            'phone' => $this->phone,
            'remark' => $this->remark,
        ];
    }
}
