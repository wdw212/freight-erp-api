<?php

use App\Http\Controllers\Api\ActivityLogsController;
use App\Http\Controllers\Api\AdminUsersController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\CompanyTypesController;
use App\Http\Controllers\Api\NoticesController;
use App\Http\Controllers\Api\NoticeTagsController;
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

    // 公司类型 - 列表
    Route::get('company-types', [CompanyTypesController::class, 'index'])
        ->name('company-types.index');
    // 公司类型 - 新增
    Route::post('company-types', [CompanyTypesController::class, 'store'])
        ->name('company-types.store');
    // 公司类型 - 详情
    Route::get('company-types/{companyType}', [CompanyTypesController::class, 'show'])
        ->name('company-types.show');
    // 公司类型 - 编辑
    Route::put('company-types/{companyType}', [CompanyTypesController::class, 'update'])
        ->name('company-types.update');
    // 公司类型 - 删除
    Route::delete('company-types/{companyType}', [CompanyTypesController::class, 'destroy'])
        ->name('company-types.destroy');

    // 公告 - 列表
    Route::get('notices', [NoticesController::class, 'index'])
        ->name('notices.index');
    // 公告 - 新增
    Route::post('notices', [NoticesController::class, 'store'])
        ->name('notices.store');
    // 公告 - 详情
    Route::get('notices/{notice}', [NoticesController::class, 'show'])
        ->name('notices.show');
    // 公告 - 编辑
    Route::put('notices/{notice}', [NoticesController::class, 'update'])
        ->name('notices.update');
    // 公告 - 删除
    Route::delete('notices/{notice}', [NoticesController::class, 'destroy'])
        ->name('notices.destroy');

    // 公告标签 - 列表
    Route::get('notice-tags', [NoticeTagsController::class, 'index'])
        ->name('notice-tags.index');
    // 公告标签 - 新增
    Route::post('notice-tags', [NoticeTagsController::class, 'store'])
        ->name('notice-tags.store');
    // 公告标签 - 详情
    Route::get('notice-tags/{noticeTag}', [NoticeTagsController::class, 'show'])
        ->name('notice-tags.show');
    // 公告标签 - 编辑
    Route::put('notice-tags/{noticeTag}', [NoticeTagsController::class, 'update'])
        ->name('notice-tags.update');
    // 公告标签 - 删除
    Route::delete('notice-tags/{noticeTag}', [NoticeTagsController::class, 'destroy'])
        ->name('notice-tags.destroy');
});
