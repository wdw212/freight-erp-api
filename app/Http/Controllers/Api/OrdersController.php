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
use App\Models\OrderDelegationHeader;
use App\Models\OrderFile;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrdersController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
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
     * @throws Throwable
     */
    public function store(OrderRequest $request, Order $order): OrderInfoResource
    {
        $order = DB::transaction(static function () use ($request, $order) {
            $data = $request->all();
            $order->fill($request->all());
            $order->is_delivery = 0;
            $order->save();

            // 单据应付款
            if (!empty($data['order_payments'])) {
                $orderPayments = json_decode($data['order_payments'], true);
                $orderPaymentRelations = [];
                foreach ($orderPayments as $orderPayment) {
                    $orderPaymentRelations[] = new OrderPayment($orderPayment);
                }
                $order->orderPayments()->saveMany($orderPaymentRelations);
            }

            // 单据委托抬头
            if (!empty($data['order_delegation_header'])) {
                $orderDelegationHeader = json_decode($data['order_delegation_header'], true);
                $orderDelegationHeader = new OrderDelegationHeader($orderDelegationHeader);
                $orderDelegationHeader->order()->associate($order);
                $orderDelegationHeader->save();
            }

            // 单据文件
            if (!empty($data['order_files'])) {
                $orderFiles = json_decode($data['order_files'], true);
                $orderFileRelations = [];
                foreach ($orderFiles as $orderFile) {
                    $orderFileRelations[] = new OrderFile($orderFile);
                }
                $order->orderFiles()->saveMany($orderFileRelations);
            }
            return $order;
        });

        return new OrderInfoResource($order);
    }

    /**
     * 详情
     * @param Order $order
     * @return OrderInfoResource
     */
    public function show(Order $order): OrderInfoResource
    {
        return new OrderInfoResource($order->load(['orderPayments', 'orderDelegationHeader', 'orderFiles']));
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
                    OrderPayment::query()->where('id', $orderPayment['id'])->update([
                        'order_id' => $orderPayment['order_id'],
                        'company_header_id' => $orderPayment['company_header_id'],
                        'no_invoice_remark' => $orderPayment['no_invoice_remark'],
                        'cny_amount' => $orderPayment['cny_amount'],
                        'cny_invoice_number' => $orderPayment['cny_invoice_number'],
                        'usd_amount' => $orderPayment['usd_amount'],
                        'usd_invoice_number' => $orderPayment['usd_invoice_number'],
                        'contact_person' => $orderPayment['contact_person'],
                        'contact_phone' => $orderPayment['contact_phone'],
                        'remark' => $orderPayment['remark'],
                    ]);
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
