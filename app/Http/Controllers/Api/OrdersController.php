<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\OrderInfoResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdersController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::query()->orderByDesc('created_at')->paginate();
        return OrderInfoResource::collection($orders);
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return OrderInfoResource
     */
    public function store(OrderRequest $request, Order $order): OrderInfoResource
    {
        $order->fill($request->all());
        $order->save();
        return new OrderInfoResource($order);
    }

    /**
     * 详情
     * @param Order $order
     * @return OrderInfoResource
     */
    public function show(Order $order): OrderInfoResource
    {
        return new OrderInfoResource($order);
    }

    public function update()
    {

    }

    public function destroy(Order $order)
    {

    }
}
