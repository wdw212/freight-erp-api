<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\CommerceOrderResource;
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
use App\Models\OrderRemark;
use Carbon\Carbon;
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
        $adminUser = $request->user();
        $orders = Order::query()->with([
            'orderType:id,name',
            'businessUser:id,name',
            'operateUser:id,name',
            'documentUser:id,name',
            'commerceUser:id,name',
            'orderDelegationHeader'
        ])->with('orderRemark', function ($query) use ($adminUser) {
            return $query->where('admin_user_id', $adminUser->id);
        })->orderByDesc('created_at')->paginate();
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

            if (!empty($data['booking_info'])) {
                $data['booking_info'] = json_decode($data['booking_info'], true);
            } else {
                $data['booking_info'] = [];
            }

            $order->fill($data);
            $order->is_delivery = 0;
            $order->save();

            // 单据应付款
            if (!empty($data['order_payments'])) {
                $orderPayments = json_decode($data['order_payments'], true);
                $orderPaymentRelations = [];
                // 应付款总计人民币
                $paymentTotalCnyAmount = collect($orderPayments)->sum('cny_amount');
                // 应付款总计美元
                $paymentTotalUsdAmount = collect($orderPayments)->sum('usd_amount');
                foreach ($orderPayments as $orderPayment) {
                    $orderPaymentRelations[] = new OrderPayment($orderPayment);
                }
                $order->orderPayments()->saveMany($orderPaymentRelations);
                $order->payment_total_cny_amount = $paymentTotalCnyAmount;
                $order->payment_total_usd_amount = $paymentTotalUsdAmount;
                $order->save();
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

                    $containerItems = $container['container_items'];
                    foreach ($containerItems as $containerItem) {
                        $containerItem = new ContainerItem($containerItem);
                        $containerItem->container()->associate($containerModel);
                        $containerItem->save();
                    }

                    $containerLoadingAddresses = $container['container_loading_addresses'];

                    foreach ($containerLoadingAddresses as $containerLoadingAddress) {
                        $containerLoadingAddress = new ContainerLoadingAddress($containerLoadingAddress);
                        $containerLoadingAddress->container()->associate($containerModel);
                        $containerLoadingAddress->save();
                    }
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
        if (!empty($data['booking_info'])) {
            $data['booking_info'] = json_decode($data['booking_info'], true);
        } else {
            $data['booking_info'] = [];
        }
        $order->fill($data);
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

    /**
     * 商务单据 - 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function commerceIndex(Request $request): AnonymousResourceCollection
    {
        // 关键词
        $keyword = $request->input('keyword');
        $startSailingDate = $request->input('start_sailing_date');
        $endSailingDate = $request->input('end_sailing_date');
        $startArrivalDate = $request->input('start_arrival_date');
        $endArrivalDate = $request->input('end_arrival_date');
        $finishingDate = $request->input('finishing_date');
        $businessUserId = $request->input('business_user_id');
        $operationUserId = $request->input('operation_user_id');
        $isDelivery = $request->input('is_delivery');
        $paymentMethod = $request->input('payment_method');
        $sellerId = $request->input('seller_id');
        $isClaimed = $request->input('is_claimed');

        $adminUser = $request->user();

        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'orderDelegationHeader'
            ])
            ->with('orderRemark', function ($query) use ($adminUser) {
                return $query->where('admin_user_id', $adminUser->id);
            })->latest();

        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->where('job_no', 'like', '%' . $keyword . '%')
                    ->orWhere('origin_port', 'like', '%' . $keyword . '%')
                    ->orWhere('bl_no', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($startSailingDate) && !empty($endSailingDate)) {
            $builder = $builder->whereBetween('sailing_at', [$startSailingDate, $endSailingDate]);
        }
        if (!empty($startArrivalDate) && !empty($endArrivalDate)) {
            $builder = $builder->whereBetween('arrival_at', [$startArrivalDate, $endArrivalDate]);
        }
        if (!empty($finishingDate)) {
            $startFinishingDate = Carbon::parse($finishingDate)->startOfMonth();
            $endFinishingDate = Carbon::parse($finishingDate)->endOfMonth();
            $builder = $builder->whereBetween('finished_at', [$startFinishingDate, $endFinishingDate]);
        }
        if (!empty($businessUserId)) {
            $builder = $builder->where('business_user_id', $businessUserId);
        }
        if (!empty($operationUserId)) {
            $builder = $builder->where('operation_user_id', $operationUserId);
        }
        if (!empty($isClaimed)) {
            $builder = $builder->where('is_claimed', 1);
        }
        $order = $builder->paginate();
        return CommerceOrderResource::collection($order);
    }

    /**
     * 财务单据 - 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function financeIndex(Request $request): AnonymousResourceCollection
    {
        // 财务单据
        $order = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'orderDelegationHeader'
            ])
            ->latest()->paginate();
        return FinanceOrderResource::collection($order);
    }

    /**
     * 更新单据备注
     * @param Request $request
     * @param Order $order
     * @return Response
     */
    public function updateRemark(Request $request, Order $order): Response
    {
        $adminUser = $request->user();
        $remark = $request->input('remark');

        $orderRemark = OrderRemark::query()
            ->where('order_id', $order->id)
            ->where('admin_user_id', $adminUser->id)
            ->first();
        if (!$orderRemark) {
            $orderRemark = new OrderRemark();
            $orderRemark->order()->associate($order);
            $orderRemark->adminUser()->associate($adminUser);
        }
        $orderRemark->remark = $remark;
        $orderRemark->save();
        return response()->noContent();
    }
}
