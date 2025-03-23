<?php

namespace App\Http\Resources\CompanyContact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $department
 * @property mixed $name
 * @property mixed $short_number
 * @property mixed $landline
 * @property mixed $phone
 */
class CompanyContactResource extends JsonResource
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
            'department' => $this->department,
            'name' => $this->name,
            'short_number' => $this->short_number,
            'landline' => $this->landline,
            'phone' => $this->phone,
        ];
    }
}
