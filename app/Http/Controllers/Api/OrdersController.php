<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\OrderInfoResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrdersController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::query()->with([
            'orderType:id,name',
            'businessUser:id,name',
        ])->orderByDesc('created_at')->paginate();
        return OrderResource::collection($orders);
    }

    /**
     * 新增
     * @param OrderRequest $request
     * @param Order $order
     * @return OrderInfoResource
     */
    public function store(OrderRequest $request, Order $order): OrderInfoResource
    {
        $data = $request->all();
        $order->fill($request->all());
        $order->save();

        // 处理应付款
        if (!empty($data['order_payments'])) {
            $orderPayments = json_decode($data['order_payments'], true);
            $orderPaymentRelations = [];
            foreach ($orderPayments as $orderPayment) {
                $orderPaymentRelations[] = new OrderPayment($orderPayment);
            }
            $order->orderPayments()->saveMany($orderPaymentRelations);
        }

        return new OrderInfoResource($order);
    }

    /**
     * 详情
     * @param Order $order
     * @return OrderInfoResource
     */
    public function show(Order $order): OrderInfoResource
    {
        return new OrderInfoResource($order->load(['orderPayments']));
    }

    /**
     * 编辑
     * @param OrderRequest $request
     * @param Order $order
     * @return OrderInfoResource
     */
    public function update(OrderRequest $request, Order $order): OrderInfoResource
    {
        $data = $request->all();
        $order->fill($request->all());
        $order->update();

        // 处理应付款
        if (!empty($data['order_payments'])) {
            $orderPayments = json_decode($data['order_payments'], true);
            $orderPaymentRelations = [];
            foreach ($orderPayments as $orderPayment) {
                if (isset($orderPayment['id'])) {
                    OrderPayment::query()->where('id', $orderPayment['id'])->update($orderPayment);
                } else {
                    $orderPaymentRelations[] = new OrderPayment($orderPayment);
                }
            }
            $order->orderPayments()->saveMany($orderPaymentRelations);
        }

        return new OrderInfoResource($order);
    }

    /**
     * 删除
     * @param Order $order
     * @return Response
     */
    public function destroy(Order $order): Response
    {
        $order->delete();
        return response()->noContent();
    }
}
