<?php
/**
 * 权限 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\Permission\PermissionInfoResource;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PermissionsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $permissions = Permission::query()
            ->with('children')
            ->where('parent_id', 0)
            ->get();
        PermissionResource::wrap('data');
        return PermissionResource::collection($permissions);
    }

    /**
     * 新增
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return PermissionInfoResource
     */
    public function store(PermissionRequest $request, Permission $permission): PermissionInfoResource
    {
        $permission->fill($request->all());
        $permission->save();
        return new PermissionInfoResource($permission);
    }

    /**
     * 详情
     * @param Permission $permission
     * @return PermissionInfoResource
     */
    public function show(Permission $permission): PermissionInfoResource
    {
        return new PermissionInfoResource($permission);
    }

    /**
     * 编辑
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return PermissionInfoResource
     */
    public function update(PermissionRequest $request, Permission $permission): PermissionInfoResource
    {
        $permission->fill($request->all());
        $permission->update();
        return new PermissionInfoResource($permission);
    }

    /**
     * 删除
     * @param Permission $permission
     * @return Response
     */
    public function destroy(Permission $permission): Response
    {
        $permission->delete();
        return response()->noContent();
    }
}
