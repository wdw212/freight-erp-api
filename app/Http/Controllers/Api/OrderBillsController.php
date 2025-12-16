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

            $cnyAmount = 0;
            $usdAmount = 0;
            // 处理账单详情
            $orderBillItems = json_decode($orderBillItems, true);
            $orderBillItemRelation = [];
            foreach ($orderBillItems as $orderBillItem) {
                $orderBillItemRelation[] = new OrderBillItem($orderBillItem);
                $orderBillItem['price'] = !empty($orderBillItem['price']) ? $orderBillItem['price'] : 0;
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
     * @throws Throwable
     */
    public function update(Request $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $orderBill = DB::transaction(static function () use ($request, $orderBill) {
            $orderBillItems = $request->input('order_bill_items');

            if (!json_validate($orderBillItems)) {
                throw new InvalidRequestException('账单详情格式错误');
            }
            $orderBill->update($request->all());
            // 处理账单详情
            $orderBillItems = json_decode($orderBillItems, true);

            // 处理需要删除的详情
            $newOrderBillItemIds = collect($orderBillItems)->pluck('id')->toArray();
            $oldOrderBillItemIds = $orderBill->orderBillItems()->pluck('id')->toArray();
            $deleteOrderBillItemIds = array_diff($oldOrderBillItemIds, $newOrderBillItemIds);
            OrderBillItem::destroy($deleteOrderBillItemIds);
            $cnyAmount = 0;
            $usdAmount = 0;
            foreach ($orderBillItems as $item) {
                if (isset($item['id'])) {
                    $orderBillItem = OrderBillItem::query()->where('id', $item['id'])->first();
                } else {
                    $orderBillItem = new OrderBillItem();
                }
                if ($item['currency'] === 'cny') {
                    if (!empty($item['price']) && !empty($item['quantity'])) {
                        $cnyAmount += $item['price'] * $item['quantity'];
                    }
                } else if (!empty($item['price']) && !empty($item['quantity'])) {
                    $usdAmount += $item['price'] * $item['quantity'];
                }
                $orderBillItem->fill($item);
                $orderBillItem->save();
            }

            // 处理账单-箱子
            $orderBillContainers = json_decode($request->input('order_bill_containers'), true);
            // 处理需要删除的账单-箱子
            $newOrderBillContainerIds = collect($orderBillContainers)->pluck('id')->toArray();
            $oldOrderBillContainerIds = $orderBill->orderBillContainers()->pluck('id')->toArray();
            $deleteOrderBillContainerIds = array_diff($newOrderBillContainerIds, $oldOrderBillContainerIds);
            OrderBillContainer::destroy($deleteOrderBillContainerIds);

            foreach ($orderBillContainers as $item) {
                if (isset($item['id'])) {
                    $orderBillContainer = OrderBillContainer::query()->where('id', $item['id'])->first();
                } else {
                    $orderBillContainer = new OrderBillContainer();
                    $orderBillContainer->orderBill()->associate($orderBill);
                }
                $orderBillContainer->fill($item);
                $orderBillContainer->save();
            }

            $orderBill->update([
                'cny_amount' => $cnyAmount,
                'usd_amount' => $usdAmount,
            ]);

            return $orderBill;
        });

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
