<?php
/**
 * 集装箱类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContainerTypeRequest;
use App\Http\Resources\ContainerType\ContainerTypeResource;
use App\Models\ContainerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ContainerTypesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate');
        if ($isPaginate) {
            $containerTypes = ContainerType::query()->orderByDesc('sort')->paginate();
        } else {
            $containerTypes = ContainerType::query()->orderByDesc('sort')->get();
            ContainerTypeResource::wrap('data');
        }
        return ContainerTypeResource::collection($containerTypes);
    }

    /**
     * 新增
     * @param ContainerTypeRequest $request
     * @param ContainerType $containerType
     * @return ContainerTypeResource
     */
    public function store(ContainerTypeRequest $request, ContainerType $containerType): ContainerTypeResource
    {
        $containerType->fill($request->all());
        $containerType->save();
        return new ContainerTypeResource($containerType);
    }

    /**
     * 详情
     * @param ContainerType $containerType
     * @return ContainerTypeResource
     */
    public function show(ContainerType $containerType): ContainerTypeResource
    {
        return new ContainerTypeResource($containerType);
    }

    /**
     * 编辑
     * @param Request $request
     * @param ContainerType $containerType
     * @return ContainerTypeResource
     */
    public function update(Request $request, ContainerType $containerType): ContainerTypeResource
    {
        $containerType->fill($request->all());
        $containerType->update();
        return new ContainerTypeResource($containerType);
    }

    /**
     * 删除
     * @param ContainerType $containerType
     * @return Response
     */
    public function destroy(ContainerType $containerType): Response
    {
        $containerType->delete();
        return response()->noContent();
    }
}
