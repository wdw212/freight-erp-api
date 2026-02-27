<?php

namespace App\Http\Resources\AdminUserSalary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $month_code
 * @property mixed $job_type
 * @property mixed $basic_salary
 * @property mixed $base_rate
 * @property mixed $higher_rate
 * @property mixed $tickets
 * @property mixed $unit_price
 * @property mixed $remark
 */
class AdminUserSalaryInfoResource extends JsonResource
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
            'job_type' => $this->job_type,
            'basic_salary' => $this->basic_salary,
            'base_rate' => $this->base_rate,
            'higher_rate' => $this->higher_rate,
            'tickets' => $this->tickets,
            'unit_price' => $this->unit_price,
            'remark' => $this->remark,
        ];
    }
}
