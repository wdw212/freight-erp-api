<?php
/**
 * 单据类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderTypeRequest;
use App\Http\Resources\OrderType\OrderTypeInfoResource;
use App\Http\Resources\OrderType\OrderTypeResource;
use App\Models\OrderType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrderTypesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate');
        $keyword = $request->input('keyword');

        $builder = OrderType::query();

        if (!empty($keyword)) {
            $builder = $builder->where('name', 'like', '%' . $keyword . '%');
        }

        if ($isPaginate) {
            $orderTypes = $builder->paginate();
        } else {
            $orderTypes = $builder->get();
            OrderTypeResource::wrap('data');
        }

        return OrderTypeResource::collection($orderTypes);
    }

    /**
     * 新增
     * @param OrderTypeRequest $request
     * @param OrderType $orderType
     * @return OrderTypeInfoResource
     */
    public function store(OrderTypeRequest $request, OrderType $orderType): OrderTypeInfoResource
    {
        $data = $request->all();

        if (!empty($data['role_ids'])) {
            $data['role_ids'] = json_decode($data['role_ids'], true);
        } else {
            $data['role_ids'] = [];
        }

        $orderType->fill($data);
        $orderType->save();
        return new OrderTypeInfoResource($orderType);
    }

    /**
     * 详情
     * @param OrderType $orderType
     * @return OrderTypeInfoResource
     */
    public function show(OrderType $orderType): OrderTypeInfoResource
    {
        return new OrderTypeInfoResource($orderType);
    }

    /**
     * 编辑
     * @param OrderTypeRequest $request
     * @param OrderType $orderType
     * @return OrderTypeInfoResource
     */
    public function update(OrderTypeRequest $request, OrderType $orderType): OrderTypeInfoResource
    {
        $data = $request->all();

        if (!empty($data['role_ids'])) {
            $data['role_ids'] = json_decode($data['role_ids'], true);
        } else {
            $data['role_ids'] = [];
        }

        $orderType->fill($data);
        $orderType->update();
        return new OrderTypeInfoResource($orderType);
    }

    /**
     * 删除
     * @param OrderType $orderType
     * @return Response
     */
    public function destroy(OrderType $orderType): Response
    {
        $orderType->delete();
        return response()->noContent();
    }
}
