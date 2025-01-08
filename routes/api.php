<?php

use App\Http\Controllers\Api\AuthorizationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 登录
Route::post('authorizations', [AuthorizationsController::class, 'store'])
    ->name('authorizations.store');

Route::group(['middleware' => 'auth:sanctum'], static function () {
    // 登录信息
    Route::get('me', [AuthorizationsController::class, 'me']);
});
