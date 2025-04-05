<?php

namespace App\Http\Resources\AdminUser;

use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $username
 * @property mixed $created_at
 * @property mixed $roles
 */
class AdminUserResource extends JsonResource
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
            'username' => $this->username,
            'roles' => RoleResource::collection($this->roles),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
