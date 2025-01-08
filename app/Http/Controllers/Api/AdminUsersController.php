<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUser\AdminUserInfoResource;
use App\Http\Resources\AdminUser\AdminUserResource;
use Illuminate\Http\Request;

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
}
