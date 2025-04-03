<?php

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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $orderTypes = OrderType::query()->paginate();
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
        $orderType->fill($request->all());
        $orderType->save();
        return new OrderTypeInfoResource($orderType);
    }

    /**
     * 详情
     * @param OrderType $orderType
     * @return OrderTypeInfoResource
     */
    public function show(OrderType $orderType)
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
        $orderType->fill($request->all());
        $orderType->update();
        return new OrderTypeInfoResource($orderType);
    }

    /**
     * 删除
     * @param OrderType $orderType
     * @return Response
     */
    public function destroy(OrderType $orderType)
    {
        $orderType->delete();
        return response()->noContent();
    }
}
