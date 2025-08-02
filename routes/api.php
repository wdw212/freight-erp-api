<?php

use App\Http\Controllers\Api\ActivityLogsController;
use App\Http\Controllers\Api\AdminUserSalariesController;
use App\Http\Controllers\Api\AdminUsersController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\CompanyContactsController;
use App\Http\Controllers\Api\CompanyContractsController;
use App\Http\Controllers\Api\CompanyHeadersController;
use App\Http\Controllers\Api\CompanyTypesController;
use App\Http\Controllers\Api\ContainerTypesController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\FleetsController;
use App\Http\Controllers\Api\LoadingAddressesController;
use App\Http\Controllers\Api\NoticesController;
use App\Http\Controllers\Api\NoticeTagsController;
use App\Http\Controllers\Api\OperationFeesController;
use App\Http\Controllers\Api\OrderFilesController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OrderTypesController;
use App\Http\Controllers\Api\PageAnnotationsController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\RegionsController;
use App\Http\Controllers\Api\RemarksController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SelectOptionsController;
use App\Http\Controllers\Api\SellersController;
use App\Http\Controllers\Api\SftRecordsController;
use App\Http\Controllers\Api\ShippingCompaniesController;
use App\Http\Controllers\Api\SpecialCostRatesController;
use App\Http\Controllers\Api\SpecialTaxRatesController;
use App\Http\Controllers\Api\TodosController;
use App\Http\Controllers\Api\UploadsController;
use App\Http\Controllers\Api\UsdExchangeRatesController;
use App\Http\Controllers\Api\WharvesController;
use App\Http\Controllers\Api\YardWharvesController;
use Illuminate\Support\Facades\Route;

// 登录
Route::post('authorizations', [AuthorizationsController::class, 'store'])
    ->name('authorizations.store');

// 上传 - 文件
Route::post('uploads/file', [UploadsController::class, 'file'])
    ->name('uploads.file');

// 选择框options
Route::get('select-options/{key}', [SelectOptionsController::class, 'index'])
    ->name('select-options.index');

// 令牌路由
Route::group(['middleware' => 'auth:sanctum'], static function () {
    // 登录信息
    Route::get('me', [AdminUsersController::class, 'me']);
    // 退出登录
    Route::delete('authorizations', [AuthorizationsController::class, 'destroy'])
        ->name('authorizations.destroy');
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
    // 角色 - 分配权限
    Route::put('roles/{role}/sync-permissions', [RolesController::class, 'syncPermissions'])
        ->name('roles.sync-permissions');


    // 权限 - 列表
    Route::get('permissions', [PermissionsController::class, 'index'])
        ->name('permissions.index');
    // 权限 - 新增
    Route::post('permissions', [PermissionsController::class, 'store'])
        ->name('permissions.store');
    // 权限 - 详情
    Route::get('permissions/{permission}', [PermissionsController::class, 'show'])
        ->name('permissions.show');
    // 权限 - 编辑
    Route::put('permissions/{permission}', [PermissionsController::class, 'update'])
        ->name('permissions.update');
    // 权限 - 删除
    Route::delete('permissions/{permission}', [PermissionsController::class, 'destroy'])
        ->name('permissions.destroy');

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
        ->name('company-headers.show')->where('companyHeader', '[0-9]+');
    // 公司抬头 - 编辑
    Route::put('company-headers/{companyHeader}', [CompanyHeadersController::class, 'update'])
        ->name('company-headers.update')->where('companyHeader', '[0-9]+');
    // 公司抬头 - 删除
    Route::delete('company-headers/{companyHeader}', [CompanyHeadersController::class, 'destroy'])
        ->name('company-headers.destroy')->where('companyHeader', '[0-9]+');
    // 公司抬头 - 分享
    Route::post('company-headers/{companyHeader}/share', [CompanyHeadersController::class, 'share'])
        ->name('company-headers.share');
    // 公司抬头 - 批量分享
    Route::post('company-headers/batch-share', [CompanyHeadersController::class, 'batchShare'])
        ->name('company-headers.batch-share');

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
    // 单据 - 详情
    Route::get('orders/{order}', [OrdersController::class, 'show'])
        ->name('orders.show');
    // 单据 - 编辑
    Route::put('orders/{order}', [OrdersController::class, 'update'])
        ->name('orders.update');
    // 单据 - 删除
    Route::delete('orders/{order}', [OrdersController::class, 'destroy'])
        ->name('orders.destroy');

    // 地区 - 列表
    Route::get('regions', [RegionsController::class, 'index'])
        ->name('regions.index');
    // 地区 - 新增
    Route::post('regions', [RegionsController::class, 'store'])
        ->name('regions.store');
    // 地区 - 详情
    Route::get('regions/{region}', [RegionsController::class, 'show'])
        ->name('regions.show');
    // 地区 - 编辑
    Route::put('regions/{region}', [RegionsController::class, 'update'])
        ->name('regions.update');
    // 地区 - 删除
    Route::delete('regions/{region}', [RegionsController::class, 'destroy'])
        ->name('regions.destroy');

    // 单据文件 - 列表
    Route::get('order-files', [OrderFilesController::class, 'index'])
        ->name('order-files.index');
    // 单据文件 - 删除
    Route::delete('order-files/{orderFile}', [OrderFilesController::class, 'destroy'])
        ->name('order-files.destroy');

    // 码头 - 列表
    Route::get('wharves', [WharvesController::class, 'index'])
        ->name('wharves.index');
    // 码头 - 新增
    Route::post('wharves', [WharvesController::class, 'store'])
        ->name('wharves.store');
    // 码头 - 详情
    Route::get('wharves/{wharf}', [WharvesController::class, 'show'])
        ->name('wharves.show');
    // 码头 - 列表
    Route::put('wharves/{wharf}', [WharvesController::class, 'update'])
        ->name('wharves.update');
    // 码头 - 删除
    Route::delete('wharves/{wharf}', [WharvesController::class, 'destroy'])
        ->name('wharves.destroy');

    // 车队 - 列表
    Route::get('fleets', [FleetsController::class, 'index'])
        ->name('fleets.index');
    // 车队 - 新增
    Route::post('fleets', [FleetsController::class, 'store'])
        ->name('fleets.store');
    // 车队 - 详情
    Route::get('fleets/{fleet}', [FleetsController::class, 'show'])
        ->name('fleets.show');
    // 车队 - 编辑
    Route::put('fleets/{fleet}', [FleetsController::class, 'update'])
        ->name('fleets.update');
    // 车队 - 删除
    Route::delete('fleets/{fleet}', [FleetsController::class, 'destroy'])
        ->name('fleets.destroy');

    // 装箱地址 - 列表
    Route::get('loading-addresses', [LoadingAddressesController::class, 'index'])
        ->name('loading-addresses.index');
    // 装箱地址 - 新增
    Route::post('loading-addresses', [LoadingAddressesController::class, 'store'])
        ->name('loading-addresses.store');
    // 装箱地址 - 详情
    Route::get('loading-addresses/{loadingAddress}', [LoadingAddressesController::class, 'show'])
        ->name('loading-addresses.show');
    // 装箱地址 - 编辑
    Route::put('loading-addresses/{loadingAddress}', [LoadingAddressesController::class, 'update'])
        ->name('loading-addresses.update');
    // 装箱地址 - 删除
    Route::delete('loading-addresses/{loadingAddress}', [LoadingAddressesController::class, 'destroy'])
        ->name('loading-addresses.destroy');

    // 账号工资 - 列表
    Route::get('admin-user-salaries', [AdminUserSalariesController::class, 'index'])
        ->name('admin-user-salaries.index');
    // 账号工资 - 新增
    Route::post('admin-user-salaries', [AdminUserSalariesController::class, 'store'])
        ->name('admin-user-salaries.store');
    // 账号工资 - 详情
    Route::get('admin-user-salaries/{adminUserSalary}', [AdminUserSalariesController::class, 'show'])
        ->name('admin-user-salaries.show');
    // 账号工资 - 编辑
    Route::put('admin-user-salaries/{adminUserSalary}', [AdminUserSalariesController::class, 'update'])
        ->name('admin-user-salaries.update');

    // 箱子类型 - 列表
    Route::get('container-types', [ContainerTypesController::class, 'index'])
        ->name('container-types.index');
    // 箱子类型 - 新增
    Route::post('container-types', [ContainerTypesController::class, 'store'])
        ->name('container-types.store');
    // 箱子类型 - 详情
    Route::get('container-types/{containerType}', [ContainerTypesController::class, 'show'])
        ->name('container-types.show');
    // 箱子类型 - 编辑
    Route::put('container-types/{containerType}', [ContainerTypesController::class, 'update'])
        ->name('container-types.update');
    // 箱子类型 - 删除
    Route::delete('container-types/{containerType}', [ContainerTypesController::class, 'destroy'])
        ->name('container-types.destroy');

    //操作费 - 列表
    Route::get('operation-fees', [OperationFeesController::class, 'index'])
        ->name('operation-fees.index');
    //操作费 - 新增
    Route::post('operation-fees', [OperationFeesController::class, 'store'])
        ->name('operation-fees.store');
    //操作费 - 详情
    Route::get('operation-fees/{operationFee}', [OperationFeesController::class, 'show'])
        ->name('operation-fees.show');
    //操作费 - 编辑
    Route::put('operation-fees/{operationFee}', [OperationFeesController::class, 'update'])
        ->name('operation-fees.update');
    //操作费 - 删除
    Route::delete('operation-fees/{operationFee}', [OperationFeesController::class, 'destroy'])
        ->name('operation-fees.destroy');

    // 预落堆场码头 - 列表
    Route::get('yard-wharves', [YardWharvesController::class, 'index'])
        ->name('yard-wharves.index');
    // 预落堆场码头 - 新增
    Route::post('yard-wharves', [YardWharvesController::class, 'store'])
        ->name('yard-wharves.store');
    // 预落堆场码头 - 详情
    Route::get('yard-wharves/{yardWharf}', [YardWharvesController::class, 'show'])
        ->name('yard-wharves.show');
    // 预落堆场码头 - 编辑
    Route::put('yard-wharves/{yardWharf}', [YardWharvesController::class, 'update'])
        ->name('yard-wharves.update');
    // 预落堆场码头 - 删除
    Route::delete('yard-wharves/{yardWharf}', [YardWharvesController::class, 'destroy'])
        ->name('yard-wharves.destroy');

    // 收发通 - 列表
    Route::get('sft-records', [SftRecordsController::class, 'index'])
        ->name('sft-records.index');
    // 收发通 - 新增
    Route::post('sft-records', [SftRecordsController::class, 'store'])
        ->name('sft-records.store');
    // 收发通 - 详情
    Route::get('sft-records/{sftRecord}', [SftRecordsController::class, 'show'])
        ->name('sft-records.show');
    // 收发通 - 编辑
    Route::put('sft-records/{sftRecord}', [SftRecordsController::class, 'update'])
        ->name('sft-records.update');
    // 收发通 - 删除
    Route::delete('sft-records/{sftRecord}', [SftRecordsController::class, 'destroy'])
        ->name('sft-records.destroy');

    // 页面注明 - 列表
    Route::get('page-annotations', [PageAnnotationsController::class, 'index'])
        ->name('page-annotations.index');
    // 页面注明 - 新增
    Route::post('page-annotations', [PageAnnotationsController::class, 'store'])
        ->name('page-annotations.store');
    // 页面注明 - 详情
    Route::get('page-annotations/{pageAnnotation}', [PageAnnotationsController::class, 'show'])
        ->name('page-annotations.show')->where('pageAnnotation', '[0-9]+');
    // 页面注明 - 编辑
    Route::put('page-annotations/{pageAnnotation}', [PageAnnotationsController::class, 'update'])
        ->name('page-annotations.update');
    // 页面注明 - 删除
    Route::delete('page-annotations/{pageAnnotation}', [PageAnnotationsController::class, 'destroy'])
        ->name('page-annotations.destroy');
    // 页面注明 - 获取模型类型
    Route::get('page-annotations/get-model-types', [PageAnnotationsController::class, 'getModelTypes'])
        ->name('page-annotations.get-model-types');
    // 页面注名 - 根据模型类型获取详情
    Route::get('page-annotations/get-show-by-model-type', [PageAnnotationsController::class, 'getShowByModelType'])
        ->name('page-annotations.get-show-by-model-type');

    // 待办事项 - 列表
    Route::get('todos', [TodosController::class, 'index'])
        ->name('todos.index');
    // 待办事项 - 新增
    Route::post('todos', [TodosController::class, 'store'])
        ->name('todos.store');
    // 待办事项 - 编辑
    Route::put('todos/{todo}', [TodosController::class, 'update'])
        ->name('todos.update');
    // 待办事项 - 删除
    Route::delete('todos/{todo}', [TodosController::class, 'destroy'])
        ->name('todos.destroy');

    // 备注 - 列表
    Route::get('remarks', [RemarksController::class, 'index'])
        ->name('remarks.index');
    // 备注 - 新增
    Route::post('remarks', [RemarksController::class, 'store'])
        ->name('remarks.store');
    // 备注 - 编辑
    Route::put('remarks/{remark}', [RemarksController::class, 'update'])
        ->name('remarks.update')->where('remark', '[0-9]+');
    // 备注 - 删除
    Route::delete('remarks/{remark}', [RemarksController::class, 'destroy'])
        ->name('remarks.destroy')->where('remark', '[0-9]+');
    // 备注 - 批量新增或创建
    Route::post('remarks/batch-store-or-update', [RemarksController::class, 'batchStoreOrUpdate'])
        ->name('remarks.batch-store-or-update');
});

