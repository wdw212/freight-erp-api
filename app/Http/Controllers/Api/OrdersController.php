<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\FinanceOrderResource;
use App\Http\Resources\Order\OrderInfoResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\ContainerLoadingAddress;
use App\Models\Order;
use App\Models\OrderDelegationHeader;
use App\Models\OrderFile;
use App\Models\OrderPayment;
use App\Models\OrderReceipt;
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

            // 单据应收款
            if (!empty($data['order_receipts'])) {
                $orderReceipts = json_decode($data['order_receipts'], true);
                $orderReceiptRelations = [];
                foreach ($orderReceipts as $orderReceipt) {
                    $orderReceiptRelations[] = new OrderReceipt($orderReceipt);
                }
                $order->orderReceipts()->saveMany($orderReceiptRelations);
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

            // 处理箱子
            if (!empty($data['containers'])) {
                $containers = json_decode($data['containers'], true);
                foreach ($containers as $container) {
                    $containerModel = new Container($container);
                    $containerModel->order()->associate($order);
                    $containerModel->save();

//                    $containerItems = $container['container_items'];
//                    foreach ($containerItems as $containerItem) {
//                        $containerItem = new ContainerItem($containerItem);
//                        $containerItem->container()->associate($containerModel);
//                        $containerItem->save();
//                    }

//                    $containerLoadingAddresses = $container['container_loading_addresses'];
//
//                    foreach ($containerLoadingAddresses as $containerLoadingAddress) {
//                        $containerLoadingAddress = new ContainerLoadingAddress($containerLoadingAddress);
//                        $containerLoadingAddress->container()->associate($containerModel);
//                        $containerLoadingAddress->save();
//                    }
                }
            }

            return $order;
        });

        return new OrderInfoResource($order->load([
            'orderPayments',
            'orderReceipts',
            'containers',
            'containers.containerItems',
            'containers.containerLoadingAddresses',
        ]));
    }

    /**
     * 详情
     * @param Order $order
     * @return OrderInfoResource
     */
    public function show(Order $order): OrderInfoResource
    {
        return new OrderInfoResource($order->load([
            'orderPayments',
            'orderReceipts',
            'orderDelegationHeader',
            'orderFiles',
            'containers',
            'containers.containerItems',
            'containers.containerLoadingAddresses',
        ]));
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
            // 处理需要删除的数据
            $oldOrderPaymentIds = OrderPayment::query()
                ->where('order_id', $order->id)
                ->pluck('id')
                ->toArray();
            $newOrderPaymentIds = collect($orderPayments)
                ->pluck('id')
                ->toArray();
            $orderPaymentIds = array_diff($oldOrderPaymentIds, $newOrderPaymentIds);
            OrderPayment::query()->whereIn('id', $orderPaymentIds)->delete();
            $orderPaymentRelations = [];
            foreach ($orderPayments as $orderPayment) {
                if (isset($orderPayment['id'])) {
                    OrderPayment::query()->where('id', $orderPayment['id'])->update([
                        'order_id' => $order->id,
                        'company_header_id' => $orderPayment['company_header_id'],
                        'no_invoice_remark' => $orderPayment['no_invoice_remark'],
                        'cny_amount' => $orderPayment['cny_amount'],
                        'cny_invoice_number' => $orderPayment['cny_invoice_number'],
                        'cny_is_cashed' => $orderPayment['cny_is_cashed'] ?? 0,
                        'usd_amount' => $orderPayment['usd_amount'],
                        'usd_invoice_number' => $orderPayment['usd_invoice_number'],
                        'usd_is_cashed' => $orderPayment['usd_is_cashed'] ?? 0,
                        'remark' => $orderPayment['remark'],
                    ]);
                } else {
                    $orderPaymentRelations[] = new OrderPayment($orderPayment);
                }
            }
            $order->orderPayments()->saveMany($orderPaymentRelations);
        }

        // 处理应收款
        if (!empty($data['order_receipts'])) {
            $orderReceipts = json_decode($data['order_receipts'], true);
            $orderReceiptRelations = [];
            foreach ($orderReceipts as $orderReceipt) {
                $orderReceiptRelations[] = new OrderReceipt($orderReceipt);
            }
            $order->orderReceipts()->saveMany($orderReceiptRelations);
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

    public function commerceIndex(Request $request)
    {
        // 商务列表
    }

    /**
     * 财务单据 - 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function financeIndex(Request $request): AnonymousResourceCollection
    {
        // 财务单据
        $order = Order::query()->latest()->paginate();
        return FinanceOrderResource::collection($order);
    }
}
