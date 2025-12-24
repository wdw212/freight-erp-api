<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $plate_number
 * @property mixed $name 司机名称
 * @property mixed $phone 电话
 * @property mixed $remark 备注
 * @property mixed $created_at
 */
class DriverResource extends JsonResource
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
            /**
             * The content of todo item, truncated.
             * @var string
             */
            'remark' => $this->remark,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
