<?php
/**
 * 角色 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\Role\RoleInfoResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate', 1);
        $builder = Role::query();
        if ($isPaginate) {
            $roles = $builder->paginate();
        } else {
            RoleResource::wrap('data');
            $roles = $builder->get();
        }
        return RoleResource::collection($roles);
    }

    /**
     * 新增
     * @param RoleRequest $request
     * @param Role $role
     * @return RoleInfoResource
     * @throws InvalidRequestException
     */
    public function store(RoleRequest $request, Role $role): RoleInfoResource
    {
        $data = $request->validated();

        $oldRole = Role::query()->where('name', $data['name'])->first();
        if ($oldRole) {
            throw new InvalidRequestException('该角色已存在，请重试！');
        }
        $role->fill($data);
        $role->save();
        return new RoleInfoResource($role);
    }

    /**
     * 详情
     * @param Role $role
     * @return RoleInfoResource
     */
    public function show(Role $role): RoleInfoResource
    {
        return new RoleInfoResource($role->load('permissions:id'));
    }

    /**
     * 编辑
     * @param RoleRequest $request
     * @param Role $role
     * @return RoleInfoResource
     */
    public function update(RoleRequest $request, Role $role): RoleInfoResource
    {
        $role->fill($request->all());
        $role->update();
        return new RoleInfoResource($role);
    }

    /**
     * 删除角色
     * @param Role $role
     * @return Response
     */
    public function destroy(Role $role): Response
    {
        Role::query()->where('id', $role->id)->delete();
        return response()->noContent();
    }

    /**
     * 分配权限
     * @param Request $request
     * @param Role $role
     * @return RoleInfoResource|Response
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $permissionIds = $request->input('permission_ids');
        if (empty($permissionIds)) {
            return response()->noContent();
        }
        $permissionIds = json_decode($permissionIds, true);
        $permissions = Permission::query()->whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
        return new RoleInfoResource($role);
    }
}
