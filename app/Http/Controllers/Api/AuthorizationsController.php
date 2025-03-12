<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorizationRequest;
use App\Models\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    /**
     * 登录获取令牌
     * @param AuthorizationRequest $request
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function store(AuthorizationRequest $request): JsonResponse
    {
        $username = $request->username;
        $password = $request->password;

        $adminUser = AdminUser::query()->where('username', $username)->first();
        if (!$adminUser) {
            throw new InvalidRequestException('账号不存在');
        }
        if (!password_verify($password, $adminUser->password)) {
            throw new InvalidRequestException('登录密码错误');
        }
        $token = $adminUser->createToken($adminUser->username)->plainTextToken;
        return response()->json([
            'token' => $token,
        ]);
    }
}
