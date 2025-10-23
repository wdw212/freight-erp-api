<?php

namespace App\Http\Resources\OrderType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $sort
 * @property mixed $role_ids
 */
class OrderTypeInfoResource extends JsonResource
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
            'name' => $this->name,
            'role_ids' => $this->role_ids,
            'sort' => (int)$this->sort,
        ];
    }
}
