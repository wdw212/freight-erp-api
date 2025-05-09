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
 * @property mixed $phone
 * @property mixed $landline
 * @property mixed $hire_date
 * @property mixed $leave_date
 * @property mixed $department
 * @property mixed $status
 * @property mixed $remark
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
            'department' => $this->department,
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phone,
            'landline' => $this->landline,
            'hire_date' => $this->hire_date,
            'leave_date' => $this->leave_date,
            'roles' => RoleResource::collection($this->roles),
            'remark' => $this->remark,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
