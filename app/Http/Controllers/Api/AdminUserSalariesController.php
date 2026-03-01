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
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminUserSalariesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUserId = $request->input('admin_user_id');
        $jobType = $request->input('job_type');
        $monthCode = $request->input('month_code');
        $pageSize = (int)$request->input('pageSize', $request->input('page_size', 15));
        $pageSize = max(1, min($pageSize, 100));

        $builder = AdminUserSalary::query()->orderByDesc('id');

        if (!empty($adminUserId)) {
            $builder->where('admin_user_id', $adminUserId);
        }
        if (!empty($jobType)) {
            $builder->where('job_type', $jobType);
        }
        if (!empty($monthCode)) {
            $builder->where('month_code', Carbon::parse($monthCode)->format('Y-m'));
        }

        $adminUserSalaries = $builder->paginate($pageSize);

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

    /**
     * 获取当月工资参数（若无记录则自动从最近月份复制延用）
     * @param Request $request
     * @return AdminUserSalaryInfoResource|JsonResponse
     * @throws InvalidRequestException
     */
    public function getCurrentMonth(Request $request): AdminUserSalaryInfoResource|JsonResponse
    {
        $adminUserId = $request->input('admin_user_id');
        if (!$adminUserId) {
            throw new InvalidRequestException('请输入账号ID');
        }

        $monthCode = $request->input('month_code')
            ? Carbon::parse($request->input('month_code'))->format('Y-m')
            : Carbon::now()->format('Y-m');

        $record = AdminUserSalary::query()
            ->where('admin_user_id', $adminUserId)
            ->where('month_code', $monthCode)
            ->first();

        if (!$record) {
            $prevRecord = AdminUserSalary::query()
                ->where('admin_user_id', $adminUserId)
                ->where('month_code', '<', $monthCode)
                ->orderByDesc('month_code')
                ->first();

            if (!$prevRecord) {
                return response()->json(['data' => null, 'message' => '暂无历史工资参数可延用']);
            }

            $record = $prevRecord->replicate();
            $record->month_code = $monthCode;
            $record->save();
        }

        return new AdminUserSalaryInfoResource($record);
    }
}
