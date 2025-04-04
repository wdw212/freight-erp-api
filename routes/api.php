<?php

use App\Http\Controllers\Api\ActivityLogsController;
use App\Http\Controllers\Api\AdminUsersController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\CompanyContactsController;
use App\Http\Controllers\Api\CompanyContractsController;
use App\Http\Controllers\Api\CompanyHeadersController;
use App\Http\Controllers\Api\CompanyTypesController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\NoticesController;
use App\Http\Controllers\Api\NoticeTagsController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OrderTypesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SelectOptionsController;
use App\Http\Controllers\Api\SellersController;
use App\Http\Controllers\Api\ShippingCompaniesController;
use App\Http\Controllers\Api\SpecialCostRatesController;
use App\Http\Controllers\Api\SpecialTaxRatesController;
use App\Http\Controllers\Api\UsdExchangeRatesController;
use Illuminate\Support\Facades\Route;

// 登录
Route::post('authorizations', [AuthorizationsController::class, 'store'])
    ->name('authorizations.store');
// 选择框options
Route::get('select-options/{key}', [SelectOptionsController::class, 'index'])
    ->name('select-options.index');
// 测试请求
Route::get('test', function () {
    return response()->json([
        'message' => '请求成功'
    ]);
});

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

    // 公司抬头 - 列表
    Route::get('company-headers', [CompanyHeadersController::class, 'index'])
        ->name('company-headers.index');
    // 公司抬头 - 新增
    Route::post('company-headers', [CompanyHeadersController::class, 'store'])
        ->name('company-headers.store');
    // 公司抬头 - 详情
    Route::get('company-headers/{companyHeader}', [CompanyHeadersController::class, 'show'])
        ->name('company-headers.show');
    // 公司抬头 - 编辑
    Route::put('company-headers/{companyHeader}', [CompanyHeadersController::class, 'update'])
        ->name('company-headers.update');
    // 公司抬头 - 删除
    Route::delete('company-headers/{companyHeader}', [CompanyHeadersController::class, 'destroy'])
        ->name('company-headers.destroy');

    // 部门 - 列表
    Route::get('departments', [DepartmentsController::class, 'index'])
        ->name('departments.index');
    // 部门 - 新增
    Route::post('departments', [DepartmentsController::class, 'store'])
        ->name('departments.store');
    // 部门 - 详情
    Route::get('departments/{department}', [DepartmentsController::class, 'show'])
        ->name('departments.show');
    // 部门 - 编辑
    Route::put('departments/{department}', [DepartmentsController::class, 'update'])
        ->name('departments.update');
    // 部门 - 删除
    Route::delete('departments/{department}', [DepartmentsController::class, 'destroy'])
        ->name('departments.destroy');

    // 公司通讯录 - 列表
    Route::get('company-contacts', [CompanyContactsController::class, 'index'])
        ->name('company-contacts.index');
    // 公司通讯录 - 新增
    Route::post('company-contacts', [CompanyContactsController::class, 'store'])
        ->name('company-contacts.store');
    // 公司通讯录 - 详情
    Route::get('company-contacts/{companyContact}', [CompanyContactsController::class, 'show'])
        ->name('company-contacts.show');
    // 公司通讯录 - 编辑
    Route::put('company-contacts/{companyContact}', [CompanyContactsController::class, 'update'])
        ->name('company-contacts.update');
    // 公司通讯录 - 删除
    Route::delete('company-contacts/{companyContact}', [CompanyContactsController::class, 'destroy'])
        ->name('company-contacts.destroy');

    // 销货单位 - 列表
    Route::get('sellers', [SellersController::class, 'index'])
        ->name('sellers.index');
    // 销货单位 - 新增
    Route::post('sellers', [SellersController::class, 'store'])
        ->name('sellers.store');
    // 销货单位 - 详情
    Route::get('sellers/{seller}', [SellersController::class, 'show'])
        ->name('sellers.show');
    // 销货单位 - 编辑
    Route::put('sellers/{seller}', [SellersController::class, 'update'])
        ->name('sellers.update');
    // 销货单位 - 删除
    Route::delete('sellers/{seller}', [SellersController::class, 'destroy'])
        ->name('sellers.destroy');

    // 公司合同 - 列表
    Route::get('company-contracts', [CompanyContractsController::class, 'index'])
        ->name('company-contracts.index');
    // 公司合同 - 新增
    Route::post('company-contracts', [CompanyContractsController::class, 'store'])
        ->name('company-contracts.store');
    // 公司合同 - 详情
    Route::get('company-contracts/{companyContract}', [CompanyContractsController::class, 'show'])
        ->name('company-contracts.show');
    // 公司合同 - 编辑
    Route::put('company-contracts/{companyContract}', [CompanyContractsController::class, 'update'])
        ->name('company-contracts.update');
    // 公司合同 - 删除
    Route::delete('company-contracts/{companyContract}', [CompanyContractsController::class, 'destroy'])
        ->name('company-contracts.destroy');

    // 特殊费用比例 - 列表
    Route::get('special-cost-rates', [SpecialCostRatesController::class, 'index'])
        ->name('special-cost-rates.index');
    // 特殊费用比例 - 新增
    Route::post('special-cost-rates', [SpecialCostRatesController::class, 'store'])
        ->name('special-cost-rates.store');
    // 特殊费用比例 - 详情
    Route::put('special-cost-rates/{specialCostRate}', [SpecialCostRatesController::class, 'update'])
        ->name('special-cost-rates.update');
    // 特殊费用比例 - 编辑
    Route::get('special-cost-rates/{specialCostRate}', [SpecialCostRatesController::class, 'show'])
        ->name('special-cost-rates.show');
    // 特殊费用比例 - 删除
    Route::delete('special-cost-rates/{specialCostRate}', [SpecialCostRatesController::class, 'destroy'])
        ->name('special-cost-rates.destroy');

    // 特殊费用税点 - 列表
    Route::get('special-tax-rates', [SpecialTaxRatesController::class, 'index'])
        ->name('special-tax-rates.index');
    // 特殊费用税点 - 新增
    Route::post('special-tax-rates', [SpecialTaxRatesController::class, 'store'])
        ->name('special-tax-rates.store');
    // 特殊费用税点 - 详情
    Route::get('special-tax-rates/{specialTaxRate}', [SpecialTaxRatesController::class, 'show'])
        ->name('special-tax-rates.show');
    // 特殊费用税点 - 编辑
    Route::put('special-tax-rates/{specialTaxRate}', [SpecialTaxRatesController::class, 'update'])
        ->name('special-tax-rates.update');

    // 每月美金汇率 - 列表
    Route::get('usd-exchange-rates', [UsdExchangeRatesController::class, 'index'])
        ->name('usd-exchange-rates.index');
    // 每月美金汇率 - 新增
    Route::post('usd-exchange-rates', [UsdExchangeRatesController::class, 'store'])
        ->name('usd-exchange-rates.store');
    // 每月美金汇率 - 详情
    Route::get('usd-exchange-rates/{usdExchangeRate}', [UsdExchangeRatesController::class, 'show'])
        ->name('usd-exchange-rates.show');
    // 每月美金汇率 - 编辑
    Route::put('usd-exchange-rates/{usdExchangeRate}', [UsdExchangeRatesController::class, 'update'])
        ->name('usd-exchange-rates.update');

    // 单据类型 - 列表
    Route::get('order-types', [OrderTypesController::class, 'index'])
        ->name('order-types.index');
    // 单据类型 - 新增
    Route::post('order-types', [OrderTypesController::class, 'store'])
        ->name('order-types.store');
    // 单据类型 - 详情
    Route::get('order-types/{orderType}', [OrderTypesController::class, 'show'])
        ->name('order-types.show');
    // 单据类型 - 编辑
    Route::put('order-types/{orderType}', [OrderTypesController::class, 'update'])
        ->name('order-types.update');
    // 单据类型 - 删除
    Route::delete('order-types/{orderType}', [OrderTypesController::class, 'destroy'])
        ->name('order-types.destroy');

    // 船公司 - 列表
    Route::get('shipping-companies', [ShippingCompaniesController::class, 'index'])
        ->name('shipping-companies.index');
    // 船公司 - 新增
    Route::post('shipping-companies', [ShippingCompaniesController::class, 'store'])
        ->name('shipping-companies.store');
    // 船公司 - 详情
    Route::get('shipping-companies/{shippingCompany}', [ShippingCompaniesController::class, 'show'])
        ->name('shipping-companies.show');
    // 船公司 - 编辑
    Route::put('shipping-companies/{shippingCompany}', [ShippingCompaniesController::class, 'update'])
        ->name('shipping-companies.update');
    // 船公司 - 删除
    Route::delete('shipping-companies/{shippingCompany}', [ShippingCompaniesController::class, 'destroy'])
        ->name('shipping-companies.destroy');

    // 单据 - 列表
    Route::get('orders', [OrdersController::class, 'index'])
        ->name('orders.index');
    // 单据 - 创建
    Route::post('orders', [OrdersController::class, 'store'])
        ->name('orders.store');
});
