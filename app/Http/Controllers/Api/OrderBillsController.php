<?php
/**
 * 鍗曟嵁璐﹀崟 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderBillRequest;
use App\Http\Resources\OrderBill\OrderBillInfoResource;
use App\Http\Resources\OrderBill\OrderBillResource;
use App\Models\FeeType;
use App\Models\OrderBill;
use App\Models\OrderBillContainer;
use App\Models\OrderBillItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderBillsController extends Controller
{
    /**
     * 解析费用类型快照名称
     * @param int|null $feeTypeId
     * @return string
     */
    private function resolveFeeTypeName(?int $feeTypeId): string
    {
        if (empty($feeTypeId)) {
            return '';
        }
        return FeeType::query()->find($feeTypeId)?->name ?? '';
    }
    /**
     * 鍒楄〃
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
     * 鏂板
     * @param OrderBillRequest $request
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     * @throws Throwable
     */
    public function store(OrderBillRequest $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $orderBill = DB::transaction(function () use ($request, $orderBill) {
            $orderBillItems = $request->input('order_bill_items');

            if (!json_validate($orderBillItems)) {
                throw new InvalidRequestException('璐﹀崟璇︽儏鏍煎紡閿欒');
            }
            // 淇濆瓨璐﹀崟淇℃伅
            $orderBill->fill($request->all());
            $orderBill->save();
            $cnyAmount = 0;
            $usdAmount = 0;

            // 澶勭悊璐﹀崟璇︽儏
            $orderBillItems = json_decode($orderBillItems, true);
            $orderBillItemRelation = [];
            foreach ($orderBillItems as $orderBillItem) {
                if (empty($orderBillItem['fee_type_id'])) {
                    unset($orderBillItem['fee_type_id']);
                    $orderBillItem['fee_type_name'] = '';
                } else {
                    $orderBillItem['fee_type_id'] = (int)$orderBillItem['fee_type_id'];
                    $orderBillItem['fee_type_name'] = $this->resolveFeeTypeName($orderBillItem['fee_type_id']);
                }
                if (empty($orderBillItem['price'])) {
                    unset($orderBillItem['price']);
                }
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

            // 澶勭悊璐﹀崟绠卞瓙
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
     * 璇︽儏
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     */
    public function show(OrderBill $orderBill): OrderBillInfoResource
    {
        return new OrderBillInfoResource($orderBill->load(['orderBillItems', 'orderBillContainers']));
    }

    /**
     * 缂栬緫
     * @param Request $request
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     * @throws Throwable
     */
    public function update(Request $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $orderBill = DB::transaction(function () use ($request, $orderBill) {
            $orderBillItems = $request->input('order_bill_items');

            if (!json_validate($orderBillItems)) {
                throw new InvalidRequestException('璐﹀崟璇︽儏鏍煎紡閿欒');
            }
            $orderBill->update($request->all());
            
            // 澶勭悊璐﹀崟璇︽儏
            $orderBillItems = json_decode($orderBillItems, true);

            // 澶勭悊闇€瑕佸垹闄ょ殑璇︽儏
            $newOrderBillItemIds = collect($orderBillItems)->pluck('id')->toArray();
            $oldOrderBillItemIds = $orderBill->orderBillItems()->pluck('id')->toArray();
            $deleteOrderBillItemIds = array_diff($oldOrderBillItemIds, $newOrderBillItemIds);
            OrderBillItem::destroy($deleteOrderBillItemIds);

            $cnyAmount = 0;
            $usdAmount = 0;
            foreach ($orderBillItems as $item) {
                if (empty($item['fee_type_id'])) {
                    unset($item['fee_type_id']);
                    $item['fee_type_name'] = '';
                } else {
                    $item['fee_type_id'] = (int)$item['fee_type_id'];
                }
                if (empty($item['price'])) {
                    unset($item['price']);
                }
                if (isset($item['id'])) {
                    $orderBillItem = OrderBillItem::query()->where('id', $item['id'])->first();
                    if (!$orderBillItem) {
                        continue;
                    }
                    $originalFeeTypeId = (int)($orderBillItem->fee_type_id ?? 0);
                    $originalFeeTypeName = (string)($orderBillItem->fee_type_name ?? '');
                    if (!empty($item['fee_type_id'])) {
                        $feeTypeIdChanged = (int)$item['fee_type_id'] !== $originalFeeTypeId;
                        $item['fee_type_name'] = ($feeTypeIdChanged || empty($originalFeeTypeName))
                            ? $this->resolveFeeTypeName($item['fee_type_id'])
                            : $originalFeeTypeName;
                    }
                } else {
                    $orderBillItem = new OrderBillItem();
                    $orderBillItem->orderBill()->associate($orderBill);
                    $item['fee_type_name'] = $this->resolveFeeTypeName($item['fee_type_id'] ?? null);
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

            // 澶勭悊璐﹀崟-绠卞瓙
            $orderBillContainers = json_decode($request->input('order_bill_containers'), true);
            // 澶勭悊闇€瑕佸垹闄ょ殑璐﹀崟-绠卞瓙
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
     * 鍒犻櫎
     * @param OrderBill $orderBill
     * @return Response
     */
    public function destroy(OrderBill $orderBill): Response
    {
        $orderBill->delete();
        return response()->noContent();
    }

    /**
     * 澶嶅埗璐﹀崟鍒版寚瀹氬崟鎹?     * @param Request $request
     * @param OrderBill $orderBill
     * @return OrderBillInfoResource
     * @throws Throwable
     */
    public function copyToOrder(Request $request, OrderBill $orderBill): OrderBillInfoResource
    {
        $adminUser = $request->user();
        $targetOrderId = (int)$request->input('order_id');
        if (empty($targetOrderId)) {
            throw new InvalidRequestException('缂哄皯鐩爣鍗曟嵁ID');
        }

        $sourceOrder = $orderBill->order()->first();
        if (!$sourceOrder) {
            throw new InvalidRequestException('婧愯处鍗曞叧鑱斿崟鎹笉瀛樺湪');
        }

        $targetOrder = Order::query()->where('id', $targetOrderId)->first();
        if (!$targetOrder) {
            throw new InvalidRequestException('目标单据不存在');
        }

        if (!$adminUser->hasRole('瓒呯') && $adminUser->hasRole('涓氬姟')) {
            if ((int)$sourceOrder->business_user_id !== (int)$adminUser->id) {
                throw new InvalidRequestException('无权复制该账单');
            }
            if ((int)$targetOrder->business_user_id !== (int)$adminUser->id) {
                throw new InvalidRequestException('鏃犳潈澶嶅埗鍒拌鍗曟嵁');
            }
        }

        $newOrderBill = DB::transaction(function () use ($orderBill, $targetOrderId) {
            $sourceOrderBill = $orderBill->load(['orderBillItems', 'orderBillContainers']);

            $newOrderBill = new OrderBill();
            $orderBillData = Arr::except($sourceOrderBill->getAttributes(), ['id', 'order_id', 'cny_amount', 'usd_amount', 'created_at', 'updated_at']);
            $orderBillData['order_id'] = $targetOrderId;
            $newOrderBill->fill($orderBillData);
            $newOrderBill->save();

            $cnyAmount = 0;
            $usdAmount = 0;
            $orderBillItemRelation = [];
            foreach ($sourceOrderBill->orderBillItems as $orderBillItem) {
                $itemData = Arr::except($orderBillItem->getAttributes(), ['id', 'order_bill_id']);
                if (empty($itemData['fee_type_id'])) {
                    unset($itemData['fee_type_id']);
                    $itemData['fee_type_name'] = '';
                } else {
                    $itemData['fee_type_id'] = (int)$itemData['fee_type_id'];
                    if (empty($itemData['fee_type_name'])) {
                        $itemData['fee_type_name'] = $this->resolveFeeTypeName($itemData['fee_type_id']);
                    }
                }
                if (empty($itemData['price'])) {
                    unset($itemData['price']);
                }

                $orderBillItemRelation[] = new OrderBillItem($itemData);

                if (($itemData['currency'] ?? '') === 'cny') {
                    if (!empty($itemData['price']) && !empty($itemData['quantity'])) {
                        $cnyAmount += $itemData['price'] * $itemData['quantity'];
                    }
                } else if (!empty($itemData['price']) && !empty($itemData['quantity'])) {
                    $usdAmount += $itemData['price'] * $itemData['quantity'];
                }
            }
            if (!empty($orderBillItemRelation)) {
                $newOrderBill->orderBillItems()->saveMany($orderBillItemRelation);
            }

            $orderBillContainerRelation = [];
            foreach ($sourceOrderBill->orderBillContainers as $orderBillContainer) {
                $containerData = Arr::except($orderBillContainer->getAttributes(), ['id', 'order_bill_id']);
                $orderBillContainerRelation[] = new OrderBillContainer($containerData);
            }
            if (!empty($orderBillContainerRelation)) {
                $newOrderBill->orderBillContainers()->saveMany($orderBillContainerRelation);
            }

            $newOrderBill->update([
                'cny_amount' => $cnyAmount,
                'usd_amount' => $usdAmount,
            ]);

            return $newOrderBill;
        });

        return new OrderBillInfoResource($newOrderBill->load(['orderBillItems', 'orderBillContainers']));
    }
}

