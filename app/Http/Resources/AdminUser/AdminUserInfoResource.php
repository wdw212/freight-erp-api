<?php

namespace App\Http\Resources\AdminUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $username
 * @property mixed $roles
 */
class AdminUserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roleId = collect($this->roles)->first()->id ?? 0;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'role_id' => $roleId,
        ];
    }
}
