<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\CommerceOrderResource;
use App\Http\Resources\Order\FinanceOrderResource;
use App\Http\Resources\Order\OrderInfoResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\CompanyHeader;
use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\ContainerLoadingAddress;
use App\Models\Order;
use App\Models\OrderBlInfo;
use App\Models\OrderDelegationHeader;
use App\Models\OrderFile;
use App\Models\OrderPayment;
use App\Models\OrderReceipt;
use App\Models\OrderRemark;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $finishAt = $request->input('finish_at');
        $adminUser = $request->user();
        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'orderDelegationHeader',
                'orderDelegationHeader.companyHeader:id,company_name',
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
        if (!empty($isDelivery)) {
            $builder = $builder->where('is_delivery', $isDelivery);
        }
        if (!empty($finishAt)) {
            $startFinishAt = Carbon::parse($finishAt)->startOfMonth();
            $endFinishAt = Carbon::parse($finishAt)->endOfMonth();
            $builder = $builder->whereBetween('finished_at', [$startFinishAt, $endFinishAt]);
        }

        $orders = $builder->paginate();

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
        Log::info('---单据新增操作---');
        $order = DB::transaction(static function () use ($request, $order) {
            $adminUser = $request->user();
            $data = $request->all();
            if (Order::query()->where('job_no', $data['job_no'])->exists()) {
                throw new InvalidRequestException('工作编号重复,请重试！');
            }
            if (!empty($data['booking_info'])) {
                $data['booking_info'] = json_decode($data['booking_info'], true);
            } else {
                $data['booking_info'] = [];
            }
            if ($adminUser->hasRole('操作')) {
                $data['operate_user_id'] = $adminUser->id;
            }
            $order->fill($data);
            $order->save();

            // 单据应付款
//            if (!empty($data['order_payments'])) {
//                $orderPayments = json_decode($data['order_payments'], true);
//                $orderPaymentRelations = [];
//                // 应付款总计人民币
//                $paymentTotalCnyAmount = collect($orderPayments)->sum('cny_amount');
//                // 应付款总计美元
//                $paymentTotalUsdAmount = collect($orderPayments)->sum('usd_amount');
//                foreach ($orderPayments as $orderPayment) {
//                    $orderPaymentRelations[] = new OrderPayment($orderPayment);
//                }
//                $order->orderPayments()->saveMany($orderPaymentRelations);
//                $order->payment_total_cny_amount = $paymentTotalCnyAmount;
//                $order->payment_total_usd_amount = $paymentTotalUsdAmount;
//                $order->save();
//            }

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
                $temp = json_decode($data['order_delegation_header'], true);
                if (!empty($temp['company_header_id'])) {
                    $companyHeader = CompanyHeader::query()->where('id', $temp['company_header_id'])->first();
                    Log::info('打印公司抬头信息');
                    $temp['contact_person'] = $companyHeader->contact_person;
                    $temp['contact_phone'] = $companyHeader->contact_phone;
                } else {
                    $temp['company_header_id'] = null;
                }
                $orderDelegationHeader = new OrderDelegationHeader();
                $orderDelegationHeader->fill($temp);
                $orderDelegationHeader->order()->associate($order);
                $orderDelegationHeader->save();
            }

            // 单据文件
            if (!empty($data['order_files'])) {
                $orderFiles = json_decode($data['order_files'], true);
                $orderFileRelations = [];
                foreach ($orderFiles as $item) {
                    $orderFileRelations[] = new OrderFile([
                        'file' => $item['file'],
                    ]);
                }
                $order->orderFiles()->saveMany($orderFileRelations);
            }

            // 处理箱子
            if (!empty($data['containers'])) {
                $containers = json_decode($data['containers'], true);
                foreach ($containers as $container) {
                    $container['no_image'] = $container['no_image']['path'] ?? '';
                    $container['seal_number_image'] = $container['seal_number_image']['path'] ?? '';
                    $container['wharf_record_image'] = $container['wharf_record_image']['path'] ?? '';
                    $container['entered_port_record_image'] = $container['entered_port_record_image']['path'] ?? '';
                    $container['drop_off_wharf_id'] = empty($container['drop_off_wharf_id']) ? null : $container['drop_off_wharf_id'];
                    $container['fleet_id'] = empty($container['fleet_id']) ? null : $container['fleet_id'];
                    $container['loading_at'] = empty($container['loading_at']) ? null : $container['loading_at'];
                    $container['wharf_id'] = empty($container['wharf_id']) ? null : $container['wharf_id'];
                    $container['pre_pull_wharf_id'] = empty($container['pre_pull_wharf_id']) ? null : $container['pre_pull_wharf_id'];
                    $container['container_type_id'] = empty($container['container_type_id']) ? null : $container['container_type_id'];
                    $containerModel = new Container();
                    $containerModel->fill($container);
                    $containerModel->order()->associate($order);
                    $containerModel->save();

                    if (isset($container['container_items'])) {
                        $containerItems = $container['container_items'];
                        foreach ($containerItems as $containerItem) {
                            $containerItem = new ContainerItem($containerItem);
                            $containerItem->container()->associate($containerModel);
                            $containerItem->save();
                        }
                    }

                    if (isset($container['container_loading_addresses'])) {
                        $containerLoadingAddresses = $container['container_loading_addresses'];
                        foreach ($containerLoadingAddresses as $containerLoadingAddress) {
                            $containerLoadingAddress = new ContainerLoadingAddress($containerLoadingAddress);
                            $containerLoadingAddress->container()->associate($containerModel);
                            $containerLoadingAddress->save();
                        }
                    }

                }
            }

            // 处理提单信息
            if (!empty($data['bl_info'])) {
                $tempBlInfo = json_decode($data['bl_info'], true);
                $orderBlInfo = new OrderBlInfo();
                $orderBlInfo->order()->associate($order);
                $orderBlInfo->fill($tempBlInfo);
                $orderBlInfo->save();
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
            'orderBlInfo'
        ]));
    }

    /**
     * 编辑
     * @param OrderRequest $request
     * @param Order $order
     * @return OrderInfoResource
     * @throws InvalidRequestException
     */
    public function update(OrderRequest $request, Order $order): OrderInfoResource
    {
        Log::info('---单据编辑操作---');
        $adminUser = $request->user();
        $data = $request->all();

        if (Order::query()->whereNot('id', $order->id)->where('job_no', $data['job_no'])->exists()) {
            throw new InvalidRequestException('工作编号重复,请重试！');
        }

        // 事务处理
        $order = DB::transaction(function () use ($data, $order, $adminUser) {
            if (!empty($data['booking_info'])) {
                $data['booking_info'] = json_decode($data['booking_info'], true);
            } else {
                $data['booking_info'] = [];
            }

            if ($adminUser->hasRole('操作')) {
                Log::info('是操作');
                $data['operate_user_id'] = $adminUser->id;
            } else {
                Log::info('不是操作');
            }

            $order->fill($data);
            $order->save();

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
                            'cny_amount' => $orderPayment['cny_amount'] ?? 0,
                            'cny_invoice_number' => $orderPayment['cny_invoice_number'],
                            'cny_is_cashed' => $orderPayment['cny_is_cashed'] ?? 0,
                            'usd_amount' => $orderPayment['usd_amount'] ?? 0,
                            'usd_invoice_number' => $orderPayment['usd_invoice_number'],
                            'usd_is_cashed' => $orderPayment['usd_is_cashed'] ?? 0,
                            'remark' => $orderPayment['remark'] ?? '',
                        ]);
                    } else {
                        $data = [
                            'company_header_id' => $orderPayment['company_header_id'],
                            'no_invoice_remark' => $orderPayment['no_invoice_remark'],
                            'cny_amount' => $orderPayment['cny_amount'] ?? 0,
                            'cny_invoice_number' => $orderPayment['cny_invoice_number'],
                            'cny_is_cashed' => $orderPayment['cny_is_cashed'] ?? 0,
                            'usd_amount' => $orderPayment['usd_amount'] ?? 0,
                            'usd_invoice_number' => $orderPayment['usd_invoice_number'],
                            'usd_is_cashed' => $orderPayment['usd_is_cashed'] ?? 0,
                            'remark' => $orderPayment['remark'] ?? '',
                        ];
                        $orderPaymentRelations[] = new OrderPayment($data);
                    }
                }
                $order->orderPayments()->saveMany($orderPaymentRelations);
            }

            // 处理应收款
            if (!empty($data['order_receipts'])) {
                $orderReceipts = json_decode($data['order_receipts'], true);
                // 处理需要应删除的数据
                $oldOrderReceiptIds = OrderReceipt::query()
                    ->where('order_id', $order->id)
                    ->pluck('id')
                    ->toArray();
                $newOrderReceiptIds = collect($orderReceipts)
                    ->pluck('id')
                    ->toArray();
                $orderReceiptIds = array_diff($oldOrderReceiptIds, $newOrderReceiptIds);
                OrderReceipt::query()->whereIn('id', $orderReceiptIds)->delete();
                $orderReceiptRelations = [];
                foreach ($orderReceipts as $orderReceipt) {
                    if (isset($orderReceipt['id'])) {
                        OrderReceipt::query()->where('id', $orderReceipt['id'])->update([
                            'order_id' => $order->id,
                            'company_header_id' => $orderReceipt['company_header_id'],
                            'no_invoice_remark' => $orderReceipt['no_invoice_remark'],
                            'cny_amount' => $orderReceipt['cny_amount'] ?? 0,
                            'cny_invoice_number' => $orderReceipt['cny_invoice_number'],
                            'cny_is_cashed' => $orderReceipt['cny_is_cashed'] ?? 0,
                            'usd_amount' => $orderReceipt['usd_amount'] ?? 0,
                            'usd_invoice_number' => $orderReceipt['usd_invoice_number'],
                            'usd_is_cashed' => $orderReceipt['usd_is_cashed'] ?? 0,
                            'remark' => $orderReceipt['remark'] ?? '',
                        ]);
                    } else {
                        $orderReceiptRelations[] = new OrderReceipt([
                            'company_header_id' => $orderReceipt['company_header_id'],
                            'no_invoice_remark' => $orderReceipt['no_invoice_remark'],
                            'cny_amount' => $orderReceipt['cny_amount'] ?? 0,
                            'cny_invoice_number' => $orderReceipt['cny_invoice_number'],
                            'cny_is_cashed' => $orderReceipt['cny_is_cashed'] ?? 0,
                            'usd_amount' => $orderReceipt['usd_amount'] ?? 0,
                            'usd_invoice_number' => $orderReceipt['usd_invoice_number'],
                            'usd_is_cashed' => $orderReceipt['usd_is_cashed'] ?? 0,
                            'remark' => $orderReceipt['remark'] ?? '',
                        ]);
                    }
                }
                $order->orderReceipts()->saveMany($orderReceiptRelations);
            }

            // 单据委托抬头
            if (!empty($data['order_delegation_header'])) {
                $temp = json_decode($data['order_delegation_header'], true);

                if (!empty($temp['company_header_id'])) {
                    $companyHeader = CompanyHeader::query()->where('id', $temp['company_header_id'])->first();
                    Log::info('打印公司抬头信息');
                    $temp['contact_person'] = $companyHeader->contact_person;
                    $item['contact_phone'] = $companyHeader->contact_phone;
                } else {
                    $item['company_header_id'] = null;
                }
                $orderDelegationHeader = OrderDelegationHeader::query()->where('order_id', $order->id)->first();
                $orderDelegationHeader->fill($temp);
                $orderDelegationHeader->order()->associate($order);
                $orderDelegationHeader->save();

            }

            // 单据文件
            if (!empty($data['order_files'])) {
                $orderFiles = json_decode($data['order_files'], true);
                $orderFileRelations = [];
                foreach ($orderFiles as $item) {
                    if (isset($item['id'])) {
                        $orderFile = OrderFile::query()->where('id', $item['id'])->first();
                        $orderFile->file = $item['file'];
                        $orderFile->update();
                    } else {
                        $orderFileRelations[] = new OrderFile([
                            'file' => $item['file'],
                        ]);
                    }
                }
                $order->orderFiles()->saveMany($orderFileRelations);
            }

            // 处理箱子
            if (!empty($data['containers'])) {
                $containers = json_decode($data['containers'], true);

                $oldContainerIds = Container::query()->where('order_id', $order->id)->pluck('id')->toArray();
                $newContainerIds = collect($containers)->pluck('id')->toArray();
                $deletedContainerIds = array_diff($oldContainerIds, $newContainerIds);

                Container::query()->whereIn('id', $deletedContainerIds)->delete();

                foreach ($containers as $container) {
                    if (isset($container['id'])) {
                        $containerModel = Container::query()
                            ->where('id', $container['id'])
                            ->first();
                    } else {
                        $containerModel = new Container();
                    }

                    $container['no_image'] = $container['no_image']['path'] ?? '';
                    $container['seal_number_image'] = $container['seal_number_image']['path'] ?? '';
                    $container['wharf_record_image'] = $container['wharf_record_image']['path'] ?? '';
                    $container['entered_port_record_image'] = $container['entered_port_record_image']['path'] ?? '';
                    $container['drop_off_wharf_id'] = empty($container['drop_off_wharf_id']) ? null : $container['drop_off_wharf_id'];
                    $container['fleet_id'] = empty($container['fleet_id']) ? null : $container['fleet_id'];
                    $container['loading_at'] = empty($container['loading_at']) ? null : $container['loading_at'];
                    $container['wharf_id'] = empty($container['wharf_id']) ? null : $container['wharf_id'];
                    $container['pre_pull_wharf_id'] = empty($container['pre_pull_wharf_id']) ? null : $container['pre_pull_wharf_id'];
                    $container['container_type_id'] = empty($container['container_type_id']) ? null : $container['container_type_id'];
                    $containerModel->fill($container);
                    $containerModel->order()->associate($order);
                    $containerModel->save();

                    if (isset($container['container_items'])) {
                        $containerItems = $container['container_items'];

                        $oldContainerItemIds = ContainerItem::query()->where('container_id', $containerModel->id)->pluck('id')->toArray();
                        $newContainerItemIds = collect($containerItems)->pluck('id')->toArray();
                        $deletedContainerItemIds = array_diff($oldContainerItemIds, $newContainerItemIds);
                        ContainerItem::query()->whereIn('id', $deletedContainerItemIds)->delete();

                        foreach ($containerItems as $containerItem) {
                            if (isset($containerItem['id'])) {
                                $containerItemModel = ContainerItem::query()->where('id', $containerItem['id'])->first();
                            } else {
                                $containerItemModel = new ContainerItem();
                            }
                            $containerItemModel->fill($containerItem);
                            $containerItemModel->container()->associate($containerModel);
                            $containerItemModel->save();
                        }
                    }

                    if (isset($container['container_loading_addresses'])) {
                        $containerLoadingAddresses = $container['container_loading_addresses'];

                        $oldContainerLoadingAddressIds = ContainerLoadingAddress::query()->where('container_id', $containerModel->id)->pluck('id')->toArray();
                        $newContainerLoadingAddressIds = collect($containerLoadingAddresses)->pluck('id')->toArray();
                        $deleteContainerLoadingAddressIds = array_diff($oldContainerLoadingAddressIds, $newContainerLoadingAddressIds);

                        ContainerLoadingAddress::query()->whereIn('id', $deleteContainerLoadingAddressIds)->delete();

                        foreach ($containerLoadingAddresses as $containerLoadingAddress) {
                            if (isset($containerLoadingAddress['id'])) {
                                $containerLoadingAddressModel = ContainerLoadingAddress::query()
                                    ->where('id', $containerLoadingAddress['id'])
                                    ->first();
                            } else {
                                $containerLoadingAddressModel = new ContainerLoadingAddress();
                            }
                            $containerLoadingAddressModel->fill($containerLoadingAddress);
                            $containerLoadingAddressModel->container()->associate($containerModel);
                            $containerLoadingAddressModel->save();
                        }
                    }

                }
            }

            // 处理提单信息
            if (!empty($data['bl_info'])) {
                $tempBlInfo = json_decode($data['bl_info'], true);
                $orderBlInfo = $order->orderBlInfo;
                if (!$orderBlInfo) {
                    $orderBlInfo = new OrderBlInfo();
                    $orderBlInfo->order()->associate($order);
                }
                $orderBlInfo->fill($tempBlInfo);
                $orderBlInfo->save();
            }

            return $order;
        });
        return new OrderInfoResource($order->load('orderBlInfo'));
    }

    /**
     * 删除
     * @param Order $order
     * @return Response
     * @throws InvalidRequestException
     */
    public function destroy(Order $order): Response
    {
        if ((int)$order->is_claimed === 1) {
            throw new InvalidRequestException('当前订单已被认领,禁止删除!');
        }
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

        if (!empty($isDelivery)) {
            $builder = $builder->where('is_delivery', $isDelivery);
        }

        $orders = $builder->paginate();
        return CommerceOrderResource::collection($orders);
    }

    /**
     * 财务单据 - 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function financeIndex(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword');
        // 财务单据
        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'orderDelegationHeader'
            ])
            ->latest();

        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->where('job_no', 'like', '%' . $keyword . '%')
                    ->orWhere('origin_port', 'like', '%' . $keyword . '%')
                    ->orWhere('bl_no', 'like', '%' . $keyword . '%');
            });
        }

        $orders = $builder->paginate();
        return FinanceOrderResource::collection($orders);
    }

    /**
     * 财务统计
     * @param Request $request
     * @return JsonResponse
     */
    public function financeStatistics(Request $request): JsonResponse
    {
        $data = [
            'receipt_total_cny_amount' => 0,
            'payment_total_cny_amount' => 0,
            'total_cny_gross_profit' => 0,
            'total_special_amount' => 0,
            'uncashed_amount' => 0,
            'cashed_amount' => 0,
            'receipt_total_usd_amount' => 0,
            'payment_total_usd_amount' => 0,
            'total_usd_gross_profit' => 0,
            'total_gross_profit' => 0
        ];
        return response()->json($data);
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

    /**
     * 认领
     * @param Order $order
     * @return Response
     */
    public function claimed(Order $order): Response
    {
        $order->is_claimed = 1;
        $order->save();
        return response()->noContent();
    }

    /**
     * 应付款完成
     * @param Order $order
     * @return JsonResponse
     */
    public function paymentFinish(Order $order): JsonResponse
    {
        Log::info('打印参数:'.$order->payment_status);
        if ((int)$order->payment_status === 1) {
            Log::info('逻辑1111');
            $order->payment_status = 0;
            $order->finish_at = null;
        } else {
            Log::info('逻辑2222');
            $order->payment_status = 1;
            $order->finish_at = Carbon::now();
        }
        $order->save();

        $finishAt = !empty($order->finish_at) ? Carbon::parse($order->finish_at)->format('Y-m-d') : '';

        return response()->json([
            'finish_at' => $finishAt
        ]);
    }
}
