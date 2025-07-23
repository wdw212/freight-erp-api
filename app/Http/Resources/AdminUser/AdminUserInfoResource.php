<?php

namespace App\Http\Resources\AdminUser;

use App\Http\Resources\Permission\PermissionResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $username
 * @property mixed $roles
 * @property mixed $department_id
 * @property mixed $phone
 * @property mixed $landline
 * @property mixed $hire_date
 * @property mixed $leave_date
 * @property mixed $remark
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $seller_ids
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
        $role = collect($this->roles)->select(['id', 'name', 'code'])->first();

        $permissionIds = collect($this->roles)->first()->permissions->pluck('id')->toArray();
        $builder = Permission::query()->where('parent_id', 0)
            ->with('children');

//        if ($role->code !== 'SUPER_ADMIN') {
//            $builder = $builder->whereIn('id', $permissionIds);
//        }

        $permissions = $builder->get();

        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phone,
            'landline' => $this->landline,
            'hire_date' => $this->hire_date,
            'leave_date' => $this->leave_date,
            'roles' => $roleId,
            'remark' => $this->remark,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'role' => $role,
            'seller_ids' => $this->seller_ids,
            'permissions' => PermissionResource::collection($permissions),
        ];
    }
}
