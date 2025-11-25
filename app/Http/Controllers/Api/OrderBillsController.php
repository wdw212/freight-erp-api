<?php
/**
 * 单据账单 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderBillRequest;
use App\Http\Resources\OrderBill\OrderBillInfoResource;
use App\Http\Resources\OrderBill\OrderBillResource;
use App\Models\OrderBill;
use App\Models\OrderBillContainer;
use App\Models\OrderBillItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderBillsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $orderId = $request->input('order_id');
        $orderBills = OrderBill::query()
            ->where('order_id', $orderId)
            ->latest()
            ->paginate();
        return OrderBillResource::collection($orderBills);
    }

    /**
     * 新增
     * @param OrderBillRequest $request
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     * @throws Throwable
     */
    public function store(OrderBillRequest $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $orderBill = DB::transaction(static function () use ($request, $orderBill) {
            $orderBillItems = $request->input('order_bill_items');

            if (!json_validate($orderBillItems)) {
                throw new InvalidRequestException('账单详情格式错误');
            }

            // 保存账单信息
            $orderBill->fill($request->all());
            $orderBill->save();

            // 处理账单详情
            $orderBillItems = json_decode($request->input('order_bill_items'), true);
            $orderBillItemRelation = [];
            $cnyAmount = 0;
            $usdAmount = 0;
            foreach ($orderBillItems as $orderBillItem) {
                $orderBillItemRelation[] = new OrderBillItem($orderBillItem);
                if ($orderBillItem['currency'] === 'cny') {
                    if (!empty($orderBillItem['price']) && !empty($orderBillItem['quantity'])) {
                        $cnyAmount += $orderBillItem['price'] * $orderBillItem['quantity'];
                    }
                } else if (!empty($orderBillItem['price']) && !empty($orderBillItem['quantity'])) {
                    $usdAmount += $orderBillItem['price'] * $orderBillItem['quantity'];
                }
            }
            $orderBill->orderBillItems()->saveMany($orderBillItemRelation);

            // 处理账单箱子
            $orderBillContainers = json_decode($request->input('order_bill_containers'), true);
            $orderBillContainerRelation = [];
            foreach ($orderBillContainers as $orderBillContainer) {
                $orderBillContainerRelation[] = new OrderBillContainer($orderBillContainer);
            }
            $orderBill->orderBillContainers()->saveMany($orderBillContainerRelation);

            $orderBill->update([
                'cny_amount' => $cnyAmount,
                'usd_amount' => $usdAmount,
            ]);

            return $orderBill;
        });
        return new OrderBillInfoResource($orderBill->load(['orderBillItems', 'orderBillContainers']));
    }

    /**
     * 详情
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     */
    public function show(OrderBill $orderBill): OrderBillInfoResource
    {
        return new OrderBillInfoResource($orderBill->load(['orderBillItems', 'orderBillContainers']));
    }

    /**
     * 编辑
     * @param Request $request
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     */
    public function update(Request $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $orderBill->update($request->all());
        return new OrderBillInfoResource($orderBill->load(['orderBillItems', 'orderBillContainers']));
    }

    /**
     * 删除
     * @param OrderBill $orderBill
     * @return Response
     */
    public function destroy(OrderBill $orderBill): Response
    {
        $orderBill->delete();
        return response()->noContent();
    }
}
