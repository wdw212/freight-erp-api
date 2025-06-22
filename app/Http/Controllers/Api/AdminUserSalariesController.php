<?php
/**
 * 账号工资 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserSalaryRequest;
use App\Http\Resources\AdminUserSalary\AdminUserSalaryInfoResource;
use App\Http\Resources\AdminUserSalary\AdminUserSalaryResource;
use App\Models\AdminUserSalary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class AdminUserSalariesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws InvalidRequestException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUserId = $request->input('admin_user_id');

        if (!$adminUserId) {
            throw new InvalidRequestException('请输入账号ID');
        }

        $adminUserSalaries = AdminUserSalary::query()->where('admin_user_id', $adminUserId)->orderByDesc('id')->paginate();
        return AdminUserSalaryResource::collection($adminUserSalaries);
    }

    /**
     * 新增
     * @param AdminUserSalaryRequest $request
     * @param AdminUserSalary $adminUserSalary
     * @return AdminUserSalaryInfoResource
     * @throws InvalidRequestException
     */
    public function store(AdminUserSalaryRequest $request, AdminUserSalary $adminUserSalary): AdminUserSalaryInfoResource
    {
        // 查询月份是否已经存在
        $oldAdminUserSalary = AdminUserSalary::query()
            ->where('admin_user_id', $request->input('admin_user_id'))
            ->where('month_code', $request->month_code)
            ->first();
        if ($oldAdminUserSalary) {
            throw new InvalidRequestException('月份已存在，请重试！');
        }

        $adminUserSalary->fill($request->all());
        $adminUserSalary->save();
        return new AdminUserSalaryInfoResource($adminUserSalary);
    }

    /**
     * 详情
     * @param AdminUserSalary $adminUserSalary
     * @return AdminUserSalaryInfoResource
     */
    public function show(AdminUserSalary $adminUserSalary): AdminUserSalaryInfoResource
    {
        return new AdminUserSalaryInfoResource($adminUserSalary);
    }

    /**
     * 编辑
     * @param Request $request
     * @param AdminUserSalary $adminUserSalary
     * @return AdminUserSalaryInfoResource
     * @throws InvalidRequestException
     */
    public function update(Request $request, AdminUserSalary $adminUserSalary): AdminUserSalaryInfoResource
    {
        // 查询月份是否已经存在
        $oldAdminUserSalary = AdminUserSalary::query()
            ->whereNot('id', $adminUserSalary->id)
            ->where('admin_user_id', $request->input('admin_user_id'))
            ->where('month_code', $request->month_code)
            ->first();
        if ($oldAdminUserSalary) {
            throw new InvalidRequestException('月份已存在，请重试！');
        }

        $adminUserSalary->fill($request->all());
        $adminUserSalary->update();
        return new AdminUserSalaryInfoResource($adminUserSalary);
    }
}
