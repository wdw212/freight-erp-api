<?php
/**
 * 部门 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\Department\DepartmentInfoResource;
use App\Http\Resources\Department\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DepartmentsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $departments = Department::query()
            ->where('parent_id', 0)
            ->with('children')
            ->get();
        DepartmentResource::wrap('data');
        return DepartmentResource::collection($departments);
    }

    /**
     * 新增
     * @param DepartmentRequest $request
     * @param Department $department
     * @return DepartmentInfoResource
     * @throws InvalidRequestException
     */
    public function store(DepartmentRequest $request, Department $department): DepartmentInfoResource
    {
        $data = $request->all();

        if (Department::query()->where('name', $data['name'])->exists()) {
            throw new InvalidRequestException('部门已存在，请重试！');
        }

        $department->fill($request->all());
        $department->save();
        return new DepartmentInfoResource($department);
    }

    /**
     * 详情
     * @param Department $department
     * @return DepartmentInfoResource
     */
    public function show(Department $department): DepartmentInfoResource
    {
        return new DepartmentInfoResource($department);
    }

    /**
     * 新增
     * @param DepartmentRequest $request
     * @param Department $department
     * @return DepartmentInfoResource
     */
    public function update(DepartmentRequest $request, Department $department): DepartmentInfoResource
    {
        $department->fill($request->all());
        $department->update();
        return new DepartmentInfoResource($department);
    }

    /**
     * 删除
     * @param Department $department
     * @return Response
     */
    public function destroy(Department $department): Response
    {
        $department->delete();
        return response()->noContent();
    }
}
