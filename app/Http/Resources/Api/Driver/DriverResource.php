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
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            /** 车牌号 */
            'plate_number' => $this->plate_number,
            /** 司机名称 */
            'name' => $this->name,
            /** 电话 */
            'phone' => $this->phone,
            /** 备注 */
            'remark' => $this->remark,
            /** 创建时间 */
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
