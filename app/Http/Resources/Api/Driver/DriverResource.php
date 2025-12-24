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
     *
     * @property int $id 司机ID [example:1, type:integer]
     * @property string $plate_number 车牌号 [example:京A12345, type:string]
     * @property string $name 司机名称 [example:张三, type:string]
     * @property string $phone 司机电话 [example:13800138000, type:string]
     * @property string|null $remark 备注信息 [example:兼职司机, type:string, nullable:true]
     * @property string $created_at 创建时间 [example:2025-12-24 10:30:00, type:string, format:date-time]
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plate_number' => $this->plate_number,
            'name' => $this->name,
            'phone' => $this->phone,
            'remark' => $this->remark,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
