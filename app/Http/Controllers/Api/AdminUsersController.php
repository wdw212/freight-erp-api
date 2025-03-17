<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\AdminUser\AdminUserInfoResource;
use App\Http\Resources\AdminUser\AdminUserResource;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminUsersController extends Controller
{
    /**
     * 登录信息
     * @param Request $request
     * @return AdminUserInfoResource
     */
    public function me(Request $request): AdminUserInfoResource
    {
        $adminUser = $request->user();
        return new AdminUserInfoResource($adminUser);
    }

    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUsers = AdminUser::query()->paginate();
        return AdminUserResource::collection($adminUsers);
    }

    /**
     * 新增
     * @param AdminUserRequest $request
     * @param AdminUser $adminUser
     * @return AdminUserInfoResource
     * @throws InvalidRequestException
     */
    public function store(AdminUserRequest $request, AdminUser $adminUser): AdminUserInfoResource
    {
        $data = $request->all();

        // 校验用户名是否存在
        $oldAdminUser = AdminUser::query()->where('username', $data['username'])->first();
        if ($oldAdminUser) {
            throw new InvalidRequestException('用户名已存在，请重试！');
        }

        $adminUser->fill($request->all());
        $adminUser->save();
        return new AdminUserInfoResource($adminUser);
    }

    /**
     * 详情
     * @param AdminUser $adminUser
     * @return AdminUserInfoResource
     */
    public function show(AdminUser $adminUser): AdminUserInfoResource
    {
        return new AdminUserInfoResource($adminUser);
    }

    /**
     * 编辑
     * @param Request $request
     * @param AdminUser $adminUser
     * @return AdminUserInfoResource
     */
    public function update(Request $request, AdminUser $adminUser): AdminUserInfoResource
    {
        $adminUser->fill($request->all());
        $adminUser->save();
        return new AdminUserInfoResource($adminUser);
    }

    /**
     * 删除
     * @param AdminUser $adminUser
     * @return Response
     */
    public function destroy(AdminUser $adminUser): Response
    {
        $adminUser->delete();
        return response()->noContent();
    }
}
