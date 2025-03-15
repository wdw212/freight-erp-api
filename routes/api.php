<?php

use App\Http\Controllers\Api\ActivityLogsController;
use App\Http\Controllers\Api\AdminUsersController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\RolesController;
use Illuminate\Support\Facades\Route;

// 登录
Route::post('authorizations', [AuthorizationsController::class, 'store'])
    ->name('authorizations.store');

// 令牌路由
Route::group(['middleware' => 'auth:sanctum'], static function () {
    // 登录信息
    Route::get('me', [AdminUsersController::class, 'me']);

    // 账号 - 列表
    Route::get('admin-users', [AdminUsersController::class, 'index'])
        ->name('admin-users.index');
    // 账号 - 新增
    Route::post('admin-users', [AdminUsersController::class, 'store'])
        ->name('admin-users.store');
    // 账号 - 详情
    Route::get('admin-users/{adminUser}', [AdminUsersController::class, 'show'])
        ->name('admin-users.show');
    // 账号 - 编辑
    Route::put('admin-users/{adminUser}', [AdminUsersController::class, 'update'])
        ->name('admin-users.update');
    // 账号 - 删除
    Route::delete('admin-users/{adminUser}', [AdminUsersController::class, 'destroy'])
        ->name('admin-users.destroy');

    // 角色 - 列表
    Route::get('roles', [RolesController::class, 'index'])
        ->name('roles.index');
    // 角色 - 新增
    Route::post('roles', [RolesController::class, 'store'])
        ->name('roles.store');
    // 角色 - 详情
    Route::get('roles/{role}', [RolesController::class, 'show'])
        ->name('roles.show');
    // 角色 - 编辑
    Route::put('roles/{role}', [RolesController::class, 'update'])
        ->name('roles.update');
    // 角色 - 删除
    Route::delete('roles/{role}', [RolesController::class, 'destroy'])
        ->name('roles.destroy');

    // 操作日志 - 列表
    Route::get('activity-logs', [ActivityLogsController::class, 'index'])
        ->name('activity-logs.index');
});
