<?php

namespace App\Http\Resources\OrderType;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $sort
 */
class OrderTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roles = [];

        if (!empty($this->role_ids)) {
            $roles = Role::query()->whereIn('id', $this->role_ids)->pluck('name')->toArray();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'roles' => $roles,
            'sort' => $this->sort
        ];
    }
}
