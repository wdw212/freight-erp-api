<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyTypeRequest;
use App\Http\Resources\CompanyType\CompanyTypeInfoResource;
use App\Http\Resources\CompanyType\CompanyTypeResource;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CompanyTypesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $companyTypes = CompanyType::query()->get();
        CompanyTypeResource::wrap('data');
        return CompanyTypeResource::collection($companyTypes);
    }

    /**
     * 新增
     * @param CompanyTypeRequest $request
     * @param CompanyType $companyType
     * @return CompanyTypeInfoResource
     * @throws InvalidRequestException
     */
    public function store(CompanyTypeRequest $request, CompanyType $companyType): CompanyTypeInfoResource
    {
        $data = $request->all();

        if (CompanyType::query()->where('name', $data['name'])->exists()) {
            throw new InvalidRequestException('类型已存在，清重试！');
        }

        if (CompanyType::query()->count() > 0) {
            if (isset($data['is_default']) && (int)$data['is_default'] === 1) {
                CompanyType::query()->update(['is_default' => 0]);
            }
        } else {
            $data['is_default'] = 1;
        }

        $companyType->fill($data);
        $companyType->save();
        return new CompanyTypeInfoResource($companyType);
    }

    /**
     * 创建
     * @param Request $request
     * @param CompanyType $companyType
     * @return CompanyTypeInfoResource
     */
    public function update(Request $request, CompanyType $companyType): CompanyTypeInfoResource
    {
        $data = $request->all();
        if (isset($data['is_default']) && (int)$data['is_default'] === 1) {
            CompanyType::query()->update(['is_default' => 0]);
        }
        $companyType->fill($request->all());
        $companyType->update();
        return new CompanyTypeInfoResource($companyType);
    }

    /**
     * 详情
     * @param CompanyType $companyType
     * @return CompanyTypeResource
     */
    public function show(CompanyType $companyType): CompanyTypeResource
    {
        return new CompanyTypeResource($companyType);
    }

    /**
     * 删除
     * @param CompanyType $companyType
     * @return Response
     */
    public function destroy(CompanyType $companyType): Response
    {
        $companyType->delete();
        return response()->noContent();
    }
}
