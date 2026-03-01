<?php
/**
 * 单据 Controller
 */

namespace App\Http\Controllers\Api;

use App\Events\OrderFinishEvent;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Order\BusinessOrderResource;
use App\Http\Resources\Order\CommerceOrderResource;
use App\Http\Resources\Order\FinanceOrderResource;
use App\Http\Resources\Order\OrderInfoResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\CompanyHeader;
use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\ContainerLoadingAddress;
use App\Models\ContainerType;
use App\Models\Fleet;
use App\Models\LoadingAddress;
use App\Models\Order;
use App\Models\ShippingCompany;
use App\Models\SftRecord;
use App\Models\Wharf;
use App\Models\OrderBlInfo;
use App\Models\OrderDelegationHeader;
use App\Models\OrderFile;
use App\Models\OrderPayment;
use App\Models\OrderReceipt;
use App\Models\OrderRemark;
use App\Models\OrderType;
use App\Models\Role;
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
        $pageSize = $request->input('page_size', 15);
        $adminUser = $request->user();
        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'shippingCompany:id,name',
                'orderDelegationHeader:id,order_id,company_header_id,company_header_name',
            ])
            ->withCount('orderFiles')
            ->with('orderRemark', function ($query) use ($adminUser) {
                return $query->where('admin_user_id', $adminUser->id);
            })->latest();

        if (!$adminUser->hasRole('超管')) {
            Log::info('---不是超管---');
            if ($adminUser->hasRole('操作')) {
                $builder = $builder->where('operate_user_id', $adminUser->id)->where('is_claimed', 1);
            }
            if ($adminUser->hasRole('业务')) {
                $builder = $builder->where('business_user_id', $adminUser->id);
            }
        }

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
        $orders = $builder->paginate($pageSize);
        return OrderResource::collection($orders);
    }

    public function store(OrderRequest $request, Order $order): OrderInfoResource
{
    $order = DB::transaction(function () use ($request, $order) {
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
        $incomingShippingCompanyName = $this->extractShippingCompanySnapshotCandidate($data);
        $data['shipping_company_id'] = empty($data['shipping_company_id'])
            ? null
            : (int)$data['shipping_company_id'];
        $data['shipping_company_name'] = $this->resolveShippingCompanySnapshotName(
            $data['shipping_company_id'],
            $incomingShippingCompanyName
        );

        if (array_key_exists('entered_port_wharf_id', $data)) {
            $data['entered_port_wharf_id'] = empty($data['entered_port_wharf_id'])
                ? null
                : (int)$data['entered_port_wharf_id'];
            $data['entered_port_wharf_name'] = $data['entered_port_wharf_id']
                ? (Wharf::query()->find($data['entered_port_wharf_id'])?->name ?? '')
                : '';
        }

        $order->fill($data);
        $order->save();

        // 单据应收款
        if (!empty($data['order_receipts'])) {
            $orderReceipts = $this->normalizeJsonArray($data['order_receipts']);
            $orderReceiptRelations = [];
            foreach ($orderReceipts as $orderReceipt) {
                $companyHeaderId = empty($orderReceipt['company_header_id'])
                    ? null
                    : (int)$orderReceipt['company_header_id'];
                $orderReceipt['company_header_id'] = $companyHeaderId;
                $orderReceipt['company_header_name'] = $this->resolveCompanyHeaderSnapshotName($companyHeaderId);
                $orderReceiptRelations[] = new OrderReceipt($orderReceipt);
            }
            $order->orderReceipts()->saveMany($orderReceiptRelations);
        }

        // 单据委托抬头
        if (!empty($data['order_delegation_header'])) {
            $temp = $this->normalizeJsonArray($data['order_delegation_header']);
            if (!empty($temp['company_header_id'])) {
                $companyHeader = CompanyHeader::query()->where('id', $temp['company_header_id'])->first();
                $temp['contact_person'] = $companyHeader?->contact_person;
                $temp['contact_phone'] = $companyHeader?->contact_phone;
                $temp['company_header_name'] = $companyHeader?->company_name ?? '';
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
            $orderFiles = $this->normalizeJsonArray($data['order_files']);
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
            $containers = $this->normalizeJsonArray($data['containers']);
            foreach ($containers as $container) {
                $container['no_image'] = $container['no_image']['path'] ?? '';
                $container['seal_number_image'] = $container['seal_number_image']['path'] ?? '';
                $container['wharf_record_image'] = $container['wharf_record_image']['path'] ?? '';
                $container['entered_port_record_image'] = $container['entered_port_record_image']['path'] ?? '';
                $container['drop_off_wharf_id'] = empty($container['drop_off_wharf_id']) ? null : (int)$container['drop_off_wharf_id'];
                $container['fleet_id'] = empty($container['fleet_id']) ? null : (int)$container['fleet_id'];
                $container['loading_at'] = empty($container['loading_at']) ? null : $container['loading_at'];
                $container['wharf_id'] = empty($container['wharf_id']) ? null : (int)$container['wharf_id'];
                $container['pre_pull_wharf_id'] = empty($container['pre_pull_wharf_id']) ? null : (int)$container['pre_pull_wharf_id'];
                $container['container_type_id'] = empty($container['container_type_id']) ? null : (int)$container['container_type_id'];
                $container['container_type_name'] = $container['container_type_id']
                    ? (ContainerType::query()->find($container['container_type_id'])?->name ?? '')
                    : '';
                $container['fleet_name'] = $container['fleet_id']
                    ? (Fleet::query()->find($container['fleet_id'])?->name ?? '')
                    : '';
                $container['pre_pull_wharf_name'] = $container['pre_pull_wharf_id']
                    ? (Wharf::query()->find($container['pre_pull_wharf_id'])?->name ?? '')
                    : '';
                $container['wharf_name'] = $container['wharf_id']
                    ? (Wharf::query()->find($container['wharf_id'])?->name ?? '')
                    : '';
                $container['drop_off_wharf_name'] = $container['drop_off_wharf_id']
                    ? (Wharf::query()->find($container['drop_off_wharf_id'])?->name ?? '')
                    : '';
                $containerModel = new Container();
                $containerModel->fill($container);
                $containerModel->order()->associate($order);
                $containerModel->save();

                if (isset($container['container_items'])) {
                    $containerItems = $this->normalizeJsonArray($container['container_items']);
                    foreach ($containerItems as $containerItem) {
                        $containerItem = new ContainerItem($containerItem);
                        $containerItem->container()->associate($containerModel);
                        $containerItem->save();
                    }
                }

                if (isset($container['container_loading_addresses'])) {
                    $containerLoadingAddresses = $this->normalizeJsonArray($container['container_loading_addresses']);
                    foreach ($containerLoadingAddresses as $containerLoadingAddress) {
                        $containerLoadingAddress = $this->normalizeJsonArray($containerLoadingAddress);
                        $loadingAddressId = empty($containerLoadingAddress['loading_address_id'])
                            ? null
                            : (int)$containerLoadingAddress['loading_address_id'];
                        $containerLoadingAddress = $this->buildContainerLoadingAddressPayload(
                            $containerLoadingAddress,
                            $loadingAddressId,
                            $this->resolveLoadingAddressSnapshotName($loadingAddressId)
                        );

                        $containerLoadingAddressModel = new ContainerLoadingAddress($containerLoadingAddress);
                        $containerLoadingAddressModel->container()->associate($containerModel);
                        $containerLoadingAddressModel->save();
                    }
                }
            }
        }

        // 处理提单信息
        if (!empty($data['bl_info'])) {
            $tempBlInfo = $this->normalizeOrderBlInfoPayload($this->normalizeJsonArray($data['bl_info']));
            $orderBlInfo = new OrderBlInfo();
            $orderBlInfo->order()->associate($order);
            $orderBlInfo->fill($tempBlInfo);
            $orderBlInfo->save();
        }

        return $order;
    });

    return new OrderInfoResource($order->load([
        'orderPayments',
        'orderPayments.companyHeader:id,company_name',
        'orderReceipts',
        'orderReceipts.companyHeader:id,company_name',
        'containers',
        'containers.containerType:id,name',
        'containers.fleet:id,name',
        'containers.prePullWharf:id,name',
        'containers.wharf:id,name',
        'containers.dropOffWharf:id,name',
        'containers.containerItems',
        'containers.containerLoadingAddresses',
        'containers.containerLoadingAddresses.loadingAddress:id,address',
        'shippingCompany:id,name',
        'enteredPortWharf:id,name',
    ]));
}

    public function show(Order $order): OrderInfoResource
{
    return new OrderInfoResource($order->load([
        'orderPayments',
        'orderPayments.companyHeader:id,company_name',
        'orderReceipts',
        'orderReceipts.companyHeader:id,company_name',
        'orderDelegationHeader',
        'orderFiles',
        'containers',
        'containers.containerType:id,name',
        'containers.fleet:id,name',
        'containers.prePullWharf:id,name',
        'containers.wharf:id,name',
        'containers.dropOffWharf:id,name',
        'containers.containerItems',
        'containers.containerLoadingAddresses',
        'containers.containerLoadingAddresses.loadingAddress:id,address',
        'orderBlInfo',
        'shippingCompany:id,name',
        'enteredPortWharf:id,name',
    ]));
}

    public function update(OrderRequest $request, Order $order): OrderInfoResource
{
    $adminUser = $request->user();
    $data = $request->all();

    if (Order::query()->whereNot('id', $order->id)->where('job_no', $data['job_no'])->exists()) {
        throw new InvalidRequestException('工作编号重复,请重试！');
    }

    if ((int)$order->is_claimed === 1 && $adminUser->hasRole('商务')) {
        throw new InvalidRequestException('当前单据已被操作认领，禁止修改!');
    }

    $order = DB::transaction(function () use ($data, $order, $adminUser) {
        $originalShippingCompanyId = (int)($order->shipping_company_id ?? 0);
        $originalShippingCompanyName = (string)($order->shipping_company_name ?? '');
        $originalEnteredPortWharfId = (int)($order->entered_port_wharf_id ?? 0);
        $originalEnteredPortWharfName = (string)($order->entered_port_wharf_name ?? '');
        $forceRefreshShippingCompanySnapshot = !empty($data['shipping_company_snapshot_refresh']);
        $forceRefreshDelegationHeaderSnapshot = !empty($data['delegation_header_snapshot_refresh']);
        $forceRefreshEnteredPortWharfSnapshot = !empty($data['entered_port_wharf_snapshot_refresh']);
        $forceRefreshContainerWharfSnapshot = !empty($data['container_wharf_snapshot_refresh']);
        unset(
            $data['shipping_company_snapshot_refresh'],
            $data['delegation_header_snapshot_refresh'],
            $data['entered_port_wharf_snapshot_refresh'],
            $data['container_wharf_snapshot_refresh']
        );

        if (!empty($data['booking_info'])) {
            $data['booking_info'] = json_decode($data['booking_info'], true);
        } else {
            $data['booking_info'] = [];
        }

        if ($adminUser->hasRole('操作')) {
            Log::info('是操作');
            $data['operate_user_id'] = $adminUser->id;
            $data['is_claimed'] = 1;
        } else {
            Log::info('不是操作');
        }

        if (array_key_exists('shipping_company_id', $data)) {
            $incomingShippingCompanyName = $this->extractShippingCompanySnapshotCandidate($data);
            $data['shipping_company_id'] = empty($data['shipping_company_id'])
                ? null
                : (int)$data['shipping_company_id'];
            $data['shipping_company_name'] = $this->resolveShippingCompanySnapshotName(
                $data['shipping_company_id'],
                $incomingShippingCompanyName,
                $originalShippingCompanyName,
                $originalShippingCompanyId,
                $forceRefreshShippingCompanySnapshot
            );
        }

        if (array_key_exists('entered_port_wharf_id', $data)) {
            $data['entered_port_wharf_id'] = empty($data['entered_port_wharf_id'])
                ? null
                : (int)$data['entered_port_wharf_id'];

            if (!empty($data['entered_port_wharf_id'])) {
                $enteredPortWharfIdChanged = (int)$data['entered_port_wharf_id'] !== $originalEnteredPortWharfId;
                if ($enteredPortWharfIdChanged || empty($originalEnteredPortWharfName) || $forceRefreshEnteredPortWharfSnapshot) {
                    $data['entered_port_wharf_name'] = Wharf::query()->find($data['entered_port_wharf_id'])?->name ?? '';
                } else {
                    $data['entered_port_wharf_name'] = $originalEnteredPortWharfName;
                }
            } else {
                $data['entered_port_wharf_name'] = '';
            }
        }

        $order->fill($data);
        $order->save();

        // 处理应付款
        if (!empty($data['order_payments'])) {
            $orderPayments = $this->normalizeJsonArray($data['order_payments']);
            $existingOrderPayments = OrderPayment::query()
                ->where('order_id', $order->id)
                ->get()
                ->keyBy('id');

            $oldOrderPaymentIds = $existingOrderPayments->keys()->toArray();
            $newOrderPaymentIds = collect($orderPayments)
                ->pluck('id')
                ->filter()
                ->map(static fn($id) => (int)$id)
                ->toArray();
            $orderPaymentIds = array_diff($oldOrderPaymentIds, $newOrderPaymentIds);
            OrderPayment::query()->whereIn('id', $orderPaymentIds)->delete();

            $orderPaymentRelations = [];
            foreach ($orderPayments as $orderPayment) {
                $orderPaymentId = empty($orderPayment['id']) ? null : (int)$orderPayment['id'];
                $existingOrderPayment = $orderPaymentId
                    ? $existingOrderPayments->get($orderPaymentId)
                    : null;

                $companyHeaderId = empty($orderPayment['company_header_id'])
                    ? null
                    : (int)$orderPayment['company_header_id'];
                $companyHeaderName = $this->resolveCompanyHeaderSnapshotName(
                    $companyHeaderId,
                    $existingOrderPayment?->company_header_name,
                    $existingOrderPayment?->company_header_id
                );

                if ($orderPaymentId) {
                    OrderPayment::query()->where('id', $orderPaymentId)->update([
                        'order_id' => $order->id,
                        'company_header_id' => $companyHeaderId,
                        'company_header_name' => $companyHeaderName,
                        'no_invoice_remark' => $orderPayment['no_invoice_remark'] ?? '',
                        'cny_amount' => $orderPayment['cny_amount'] ?? 0,
                        'cny_invoice_number' => $orderPayment['cny_invoice_number'] ?? '',
                        'cny_is_cashed' => $orderPayment['cny_is_cashed'] ?? 0,
                        'usd_amount' => $orderPayment['usd_amount'] ?? 0,
                        'usd_invoice_number' => $orderPayment['usd_invoice_number'] ?? '',
                        'usd_is_cashed' => $orderPayment['usd_is_cashed'] ?? 0,
                        'remark' => $orderPayment['remark'] ?? '',
                    ]);
                } else {
                    $orderPaymentData = [
                        'company_header_id' => $companyHeaderId,
                        'company_header_name' => $companyHeaderName,
                        'no_invoice_remark' => $orderPayment['no_invoice_remark'] ?? '',
                        'cny_amount' => $orderPayment['cny_amount'] ?? 0,
                        'cny_invoice_number' => $orderPayment['cny_invoice_number'] ?? '',
                        'cny_is_cashed' => $orderPayment['cny_is_cashed'] ?? 0,
                        'usd_amount' => $orderPayment['usd_amount'] ?? 0,
                        'usd_invoice_number' => $orderPayment['usd_invoice_number'] ?? '',
                        'usd_is_cashed' => $orderPayment['usd_is_cashed'] ?? 0,
                        'remark' => $orderPayment['remark'] ?? '',
                    ];
                    $orderPaymentRelations[] = new OrderPayment($orderPaymentData);
                }
            }
            $order->orderPayments()->saveMany($orderPaymentRelations);

            $cnyAmount = $order->orderPayments()->sum('cny_amount');
            $usdAmount = $order->orderPayments()->sum('usd_amount');
            $order->payment_total_cny_amount = $cnyAmount;
            $order->payment_total_usd_amount = $usdAmount;
        }

        // 处理应收款
        if (!empty($data['order_receipts'])) {
            $orderReceipts = $this->normalizeJsonArray($data['order_receipts']);
            $existingOrderReceipts = OrderReceipt::query()
                ->where('order_id', $order->id)
                ->get()
                ->keyBy('id');

            $oldOrderReceiptIds = $existingOrderReceipts->keys()->toArray();
            $newOrderReceiptIds = collect($orderReceipts)
                ->pluck('id')
                ->filter()
                ->map(static fn($id) => (int)$id)
                ->toArray();
            $orderReceiptIds = array_diff($oldOrderReceiptIds, $newOrderReceiptIds);
            OrderReceipt::query()->whereIn('id', $orderReceiptIds)->delete();

            $orderReceiptRelations = [];
            foreach ($orderReceipts as $orderReceipt) {
                $orderReceiptId = empty($orderReceipt['id']) ? null : (int)$orderReceipt['id'];
                $existingOrderReceipt = $orderReceiptId
                    ? $existingOrderReceipts->get($orderReceiptId)
                    : null;

                $companyHeaderId = empty($orderReceipt['company_header_id'])
                    ? null
                    : (int)$orderReceipt['company_header_id'];
                $companyHeaderName = $this->resolveCompanyHeaderSnapshotName(
                    $companyHeaderId,
                    $existingOrderReceipt?->company_header_name,
                    $existingOrderReceipt?->company_header_id
                );

                if ($orderReceiptId) {
                    OrderReceipt::query()->where('id', $orderReceiptId)->update([
                        'order_id' => $order->id,
                        'company_header_id' => $companyHeaderId,
                        'company_header_name' => $companyHeaderName,
                        'cny_amount' => $orderReceipt['cny_amount'] ?? 0,
                        'cny_invoice_number' => $orderReceipt['cny_invoice_number'] ?? '',
                        'cny_is_cashed' => $orderReceipt['cny_is_cashed'] ?? 0,
                        'usd_amount' => $orderReceipt['usd_amount'] ?? 0,
                        'usd_invoice_number' => $orderReceipt['usd_invoice_number'] ?? '',
                        'usd_is_cashed' => $orderReceipt['usd_is_cashed'] ?? 0,
                        'remark' => $orderReceipt['remark'] ?? '',
                    ]);
                } else {
                    $orderReceiptRelations[] = new OrderReceipt([
                        'company_header_id' => $companyHeaderId,
                        'company_header_name' => $companyHeaderName,
                        'cny_amount' => $orderReceipt['cny_amount'] ?? 0,
                        'cny_invoice_number' => $orderReceipt['cny_invoice_number'] ?? '',
                        'cny_is_cashed' => $orderReceipt['cny_is_cashed'] ?? 0,
                        'usd_amount' => $orderReceipt['usd_amount'] ?? 0,
                        'usd_invoice_number' => $orderReceipt['usd_invoice_number'] ?? '',
                        'usd_is_cashed' => $orderReceipt['usd_is_cashed'] ?? 0,
                        'remark' => $orderReceipt['remark'] ?? '',
                    ]);
                }
            }
            $order->orderReceipts()->saveMany($orderReceiptRelations);

            $cnyAmount = $order->orderReceipts()->sum('cny_amount');
            $usdAmount = $order->orderReceipts()->sum('usd_amount');
            $order->receipt_total_cny_amount = $cnyAmount;
            $order->receipt_total_usd_amount = $usdAmount;
        }

        // 单据委托抬头
        if (!empty($data['order_delegation_header'])) {
            $temp = $this->normalizeJsonArray($data['order_delegation_header']);
            $orderDelegationHeader = OrderDelegationHeader::query()->where('order_id', $order->id)->first();
            if (!$orderDelegationHeader) {
                $orderDelegationHeader = new OrderDelegationHeader();
                $orderDelegationHeader->order()->associate($order);
            }

            $originalCompanyHeaderId = (int)($orderDelegationHeader->company_header_id ?? 0);
            $originalCompanyHeaderName = (string)($orderDelegationHeader->company_header_name ?? '');
            $originalContactPerson = $orderDelegationHeader->contact_person;
            $originalContactPhone = $orderDelegationHeader->contact_phone;

            if (!empty($temp['company_header_id'])) {
                $temp['company_header_id'] = (int)$temp['company_header_id'];
                $companyHeaderIdChanged = $temp['company_header_id'] !== $originalCompanyHeaderId;
                if ($companyHeaderIdChanged || empty($originalCompanyHeaderName) || $forceRefreshDelegationHeaderSnapshot) {
                    $companyHeader = CompanyHeader::query()->where('id', $temp['company_header_id'])->first();
                    $temp['contact_person'] = $companyHeader?->contact_person;
                    $temp['contact_phone'] = $companyHeader?->contact_phone;
                    $temp['company_header_name'] = $companyHeader?->company_name ?? '';
                } else {
                    $temp['contact_person'] = $originalContactPerson;
                    $temp['contact_phone'] = $originalContactPhone;
                    $temp['company_header_name'] = $originalCompanyHeaderName;
                }
            } else {
                $temp['company_header_id'] = null;
                $temp['company_header_name'] = '';
            }
            $orderDelegationHeader->fill($temp);
            $orderDelegationHeader->save();
        }

        // 单据文件
        if (!empty($data['order_files'])) {
            $orderFiles = $this->normalizeJsonArray($data['order_files']);
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
            $containers = $this->normalizeJsonArray($data['containers']);

            $oldContainerIds = Container::query()->where('order_id', $order->id)->pluck('id')->toArray();
            $newContainerIds = collect($containers)->pluck('id')->toArray();
            $deletedContainerIds = array_diff($oldContainerIds, $newContainerIds);
            Container::query()->whereIn('id', $deletedContainerIds)->delete();

            foreach ($containers as $container) {
                if (isset($container['id'])) {
                    $containerModel = Container::query()->where('id', $container['id'])->first();
                    if (!$containerModel) {
                        $containerModel = new Container();
                    }
                } else {
                    $containerModel = new Container();
                }

                $originalContainerTypeId = (int)($containerModel->container_type_id ?? 0);
                $originalContainerTypeName = (string)($containerModel->container_type_name ?? '');
                $originalFleetId = (int)($containerModel->fleet_id ?? 0);
                $originalFleetName = (string)($containerModel->fleet_name ?? '');
                $originalPrePullWharfId = (int)($containerModel->pre_pull_wharf_id ?? 0);
                $originalPrePullWharfName = (string)($containerModel->pre_pull_wharf_name ?? '');
                $originalWharfId = (int)($containerModel->wharf_id ?? 0);
                $originalWharfName = (string)($containerModel->wharf_name ?? '');
                $originalDropOffWharfId = (int)($containerModel->drop_off_wharf_id ?? 0);
                $originalDropOffWharfName = (string)($containerModel->drop_off_wharf_name ?? '');

                $container['no_image'] = $container['no_image']['path'] ?? '';
                $container['seal_number_image'] = $container['seal_number_image']['path'] ?? '';
                $container['wharf_record_image'] = $container['wharf_record_image']['path'] ?? '';
                $container['entered_port_record_image'] = $container['entered_port_record_image']['path'] ?? '';
                $container['drop_off_wharf_id'] = empty($container['drop_off_wharf_id']) ? null : (int)$container['drop_off_wharf_id'];
                $container['fleet_id'] = empty($container['fleet_id']) ? null : (int)$container['fleet_id'];
                $container['loading_at'] = empty($container['loading_at']) ? null : $container['loading_at'];
                $container['wharf_id'] = empty($container['wharf_id']) ? null : (int)$container['wharf_id'];
                $container['pre_pull_wharf_id'] = empty($container['pre_pull_wharf_id']) ? null : (int)$container['pre_pull_wharf_id'];
                $container['container_type_id'] = empty($container['container_type_id']) ? null : (int)$container['container_type_id'];

                if (!empty($container['container_type_id'])) {
                    $containerTypeIdChanged = (int)$container['container_type_id'] !== $originalContainerTypeId;
                    if ($containerTypeIdChanged || empty($originalContainerTypeName)) {
                        $container['container_type_name'] = ContainerType::query()->find($container['container_type_id'])?->name ?? '';
                    } else {
                        $container['container_type_name'] = $originalContainerTypeName;
                    }
                } else {
                    $container['container_type_name'] = '';
                }

                if (!empty($container['fleet_id'])) {
                    $fleetIdChanged = (int)$container['fleet_id'] !== $originalFleetId;
                    if ($fleetIdChanged || empty($originalFleetName)) {
                        $container['fleet_name'] = Fleet::query()->find($container['fleet_id'])?->name ?? '';
                    } else {
                        $container['fleet_name'] = $originalFleetName;
                    }
                } else {
                    $container['fleet_name'] = '';
                }

                if (!empty($container['pre_pull_wharf_id'])) {
                    $prePullWharfIdChanged = (int)$container['pre_pull_wharf_id'] !== $originalPrePullWharfId;
                    if ($prePullWharfIdChanged || empty($originalPrePullWharfName) || $forceRefreshContainerWharfSnapshot) {
                        $container['pre_pull_wharf_name'] = Wharf::query()->find($container['pre_pull_wharf_id'])?->name ?? '';
                    } else {
                        $container['pre_pull_wharf_name'] = $originalPrePullWharfName;
                    }
                } else {
                    $container['pre_pull_wharf_name'] = '';
                }

                if (!empty($container['wharf_id'])) {
                    $wharfIdChanged = (int)$container['wharf_id'] !== $originalWharfId;
                    if ($wharfIdChanged || empty($originalWharfName) || $forceRefreshContainerWharfSnapshot) {
                        $container['wharf_name'] = Wharf::query()->find($container['wharf_id'])?->name ?? '';
                    } else {
                        $container['wharf_name'] = $originalWharfName;
                    }
                } else {
                    $container['wharf_name'] = '';
                }

                if (!empty($container['drop_off_wharf_id'])) {
                    $dropOffWharfIdChanged = (int)$container['drop_off_wharf_id'] !== $originalDropOffWharfId;
                    if ($dropOffWharfIdChanged || empty($originalDropOffWharfName) || $forceRefreshContainerWharfSnapshot) {
                        $container['drop_off_wharf_name'] = Wharf::query()->find($container['drop_off_wharf_id'])?->name ?? '';
                    } else {
                        $container['drop_off_wharf_name'] = $originalDropOffWharfName;
                    }
                } else {
                    $container['drop_off_wharf_name'] = '';
                }

                $containerModel->fill($container);
                $containerModel->order()->associate($order);
                $containerModel->save();

                if (isset($container['container_items'])) {
                    $containerItems = $this->normalizeJsonArray($container['container_items']);

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
                    $containerLoadingAddresses = $this->normalizeJsonArray($container['container_loading_addresses']);
                    $existingContainerLoadingAddresses = ContainerLoadingAddress::query()
                        ->where('container_id', $containerModel->id)
                        ->get()
                        ->keyBy('id');

                    $oldContainerLoadingAddressIds = $existingContainerLoadingAddresses->keys()->toArray();
                    $newContainerLoadingAddressIds = collect($containerLoadingAddresses)
                        ->pluck('id')
                        ->filter()
                        ->map(static fn($id) => (int)$id)
                        ->toArray();
                    $deleteContainerLoadingAddressIds = array_diff($oldContainerLoadingAddressIds, $newContainerLoadingAddressIds);
                    ContainerLoadingAddress::query()->whereIn('id', $deleteContainerLoadingAddressIds)->delete();

                    foreach ($containerLoadingAddresses as $containerLoadingAddress) {
                        $containerLoadingAddress = $this->normalizeJsonArray($containerLoadingAddress);
                        $containerLoadingAddressId = empty($containerLoadingAddress['id'])
                            ? null
                            : (int)$containerLoadingAddress['id'];
                        $existingContainerLoadingAddress = $containerLoadingAddressId
                            ? $existingContainerLoadingAddresses->get($containerLoadingAddressId)
                            : null;

                        $loadingAddressId = empty($containerLoadingAddress['loading_address_id'])
                            ? null
                            : (int)$containerLoadingAddress['loading_address_id'];
                        $containerLoadingAddress = $this->buildContainerLoadingAddressPayload(
                            $containerLoadingAddress,
                            $loadingAddressId,
                            $this->resolveLoadingAddressSnapshotName(
                                $loadingAddressId,
                                $existingContainerLoadingAddress?->loading_address,
                                $existingContainerLoadingAddress?->loading_address_id
                            )
                        );

                        if ($containerLoadingAddressId) {
                            $containerLoadingAddressModel = ContainerLoadingAddress::query()->where('id', $containerLoadingAddressId)->first();
                            if (!$containerLoadingAddressModel) {
                                $containerLoadingAddressModel = new ContainerLoadingAddress();
                            }
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
            $tempBlInfo = $this->normalizeOrderBlInfoPayload(
                $this->normalizeJsonArray($data['bl_info']),
                $order->orderBlInfo
            );
            $orderBlInfo = $order->orderBlInfo;
            if (!$orderBlInfo) {
                $orderBlInfo = new OrderBlInfo();
                $orderBlInfo->order()->associate($order);
            }
            $orderBlInfo->fill($tempBlInfo);
            $orderBlInfo->save();
        }

        $order->save();
        return $order;
    });

    return new OrderInfoResource($order->load([
        'orderPayments',
        'orderPayments.companyHeader:id,company_name',
        'orderReceipts',
        'orderReceipts.companyHeader:id,company_name',
        'orderDelegationHeader',
        'orderFiles',
        'orderBlInfo',
        'shippingCompany:id,name',
        'enteredPortWharf:id,name',
        'containers',
        'containers.containerType:id,name',
        'containers.fleet:id,name',
        'containers.prePullWharf:id,name',
        'containers.wharf:id,name',
        'containers.dropOffWharf:id,name',
        'containers.containerItems',
        'containers.containerLoadingAddresses',
        'containers.containerLoadingAddresses.loadingAddress:id,address',
    ]));
}

    /**
     * 从请求中提取可用的船公司名称快照（优先字符串字段，其次详情对象里的 name）。
     */
    private function extractShippingCompanySnapshotCandidate(array $data): string
    {
        $candidates = [
            $data['shipping_company_name'] ?? null,
            $data['shipping_company'] ?? null,
        ];

        if (array_key_exists('shipping_company_detail', $data)) {
            $detail = $this->normalizeJsonArray($data['shipping_company_detail']);
            $candidates[] = $detail['name'] ?? null;
        }

        foreach ($candidates as $candidate) {
            $name = trim((string)($candidate ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return '';
    }

    /**
     * 解析船公司名称快照：
     * 1) 主数据存在时使用主数据名称；
     * 2) 主数据缺失时保留入参快照名；
     * 3) 同 ID 更新且已有旧快照时保留旧快照（除非强制刷新且主数据可用）。
     */
    private function resolveShippingCompanySnapshotName(
        ?int $shippingCompanyId,
        mixed $incomingSnapshotName = '',
        mixed $originalSnapshotName = '',
        mixed $originalShippingCompanyId = null,
        bool $forceRefresh = false
    ): string {
        if (empty($shippingCompanyId)) {
            return '';
        }

        $shippingCompanyId = (int)$shippingCompanyId;
        $incomingName = trim((string)($incomingSnapshotName ?? ''));
        $oldName = trim((string)($originalSnapshotName ?? ''));
        if (ctype_digit($incomingName) && (int)$incomingName === $shippingCompanyId) {
            $incomingName = '';
        }
        if (ctype_digit($oldName) && (int)$oldName === $shippingCompanyId) {
            $oldName = '';
        }
        $oldShippingCompanyId = empty($originalShippingCompanyId) ? null : (int)$originalShippingCompanyId;
        $isSameCompany = !empty($oldShippingCompanyId) && $oldShippingCompanyId === $shippingCompanyId;

        if (!$forceRefresh && $isSameCompany && $oldName !== '') {
            return $oldName;
        }

        $resolvedName = trim((string)(ShippingCompany::query()->find($shippingCompanyId)?->name ?? ''));
        if ($resolvedName !== '') {
            return $resolvedName;
        }

        if ($incomingName !== '') {
            return $incomingName;
        }

        return $isSameCompany ? $oldName : '';
    }

    private function resolveCompanyHeaderSnapshotName(
        ?int $companyHeaderId,
        mixed $originalCompanyHeaderName = '',
        mixed $originalCompanyHeaderId = null
    ): string {
        if (empty($companyHeaderId)) {
            return '';
        }

        $companyHeaderId = (int)$companyHeaderId;
        $oldCompanyHeaderId = empty($originalCompanyHeaderId) ? null : (int)$originalCompanyHeaderId;
        $oldCompanyHeaderName = trim((string)($originalCompanyHeaderName ?? ''));

        if (!empty($oldCompanyHeaderId) && $oldCompanyHeaderId === $companyHeaderId && $oldCompanyHeaderName !== '') {
            return $oldCompanyHeaderName;
        }

        return CompanyHeader::query()->find($companyHeaderId)?->company_name ?? '';
    }

    private function resolveLoadingAddressSnapshotName(
        ?int $loadingAddressId,
        mixed $originalLoadingAddressName = '',
        mixed $originalLoadingAddressId = null
    ): string {
        if (empty($loadingAddressId)) {
            return '';
        }

        $loadingAddressId = (int)$loadingAddressId;
        $oldLoadingAddressId = empty($originalLoadingAddressId) ? null : (int)$originalLoadingAddressId;
        $oldLoadingAddressName = trim((string)($originalLoadingAddressName ?? ''));

        if (!empty($oldLoadingAddressId) && $oldLoadingAddressId === $loadingAddressId && $oldLoadingAddressName !== '') {
            return $oldLoadingAddressName;
        }

        return LoadingAddress::query()->find($loadingAddressId)?->address ?? '';
    }

    private function buildContainerLoadingAddressPayload(
        array $containerLoadingAddress,
        ?int $loadingAddressId,
        string $loadingAddressSnapshotName
    ): array {
        return [
            'loading_address_id' => $loadingAddressId,
            'loading_address' => $loadingAddressSnapshotName,
            'address' => $containerLoadingAddress['address'] ?? '',
            'contact_name' => $containerLoadingAddress['contact_name'] ?? '',
            'phone' => $containerLoadingAddress['phone'] ?? '',
            'remark' => $containerLoadingAddress['remark'] ?? '',
        ];
    }

    private function normalizeOrderBlInfoPayload(array $payload, ?OrderBlInfo $existingOrderBlInfo = null): array
    {
        foreach (['sender', 'receiver', 'notifier'] as $partyKey) {
            $partyIdKey = $partyKey . '_id';
            $hasPartyPayload = array_key_exists($partyKey, $payload);
            $hasPartyIdPayload = array_key_exists($partyIdKey, $payload);

            $existingPartyId = empty($existingOrderBlInfo?->{$partyIdKey})
                ? null
                : (int)$existingOrderBlInfo->{$partyIdKey};
            $existingPartySnapshot = $this->normalizeJsonArray($existingOrderBlInfo?->{$partyKey});

            if (!$hasPartyPayload && !$hasPartyIdPayload) {
                $payload[$partyKey] = $existingPartySnapshot;
                $payload[$partyIdKey] = $existingPartyId;
                continue;
            }

            $partyId = $hasPartyIdPayload
                ? (empty($payload[$partyIdKey]) ? null : (int)$payload[$partyIdKey])
                : $existingPartyId;

            $partySnapshotPayload = $hasPartyPayload ? $payload[$partyKey] : null;
            $normalizedSnapshot = $this->normalizeOrderBlPartySnapshot(
                $partySnapshotPayload,
                $partyId,
                $existingPartyId,
                $existingPartySnapshot
            );

            $payload[$partyKey] = $normalizedSnapshot;
            $payload[$partyIdKey] = empty($normalizedSnapshot['id']) ? null : (int)$normalizedSnapshot['id'];
        }

        return $payload;
    }

    private function normalizeOrderBlPartySnapshot(
        mixed $partySnapshotPayload,
        ?int $partyId,
        ?int $existingPartyId = null,
        array $existingPartySnapshot = []
    ): array {
        $incomingSnapshot = $this->normalizeJsonArray($partySnapshotPayload);

        if (!empty($partyId)) {
            if (!empty($existingPartyId) && $partyId === $existingPartyId && !empty($existingPartySnapshot)) {
                $existingPartySnapshot['id'] = $partyId;
                return $existingPartySnapshot;
            }

            $resolvedSnapshot = $this->buildSftSnapshotById($partyId);
            if (!empty($resolvedSnapshot)) {
                return $resolvedSnapshot;
            }

            if (!empty($incomingSnapshot)) {
                $incomingSnapshot['id'] = $partyId;
                return $incomingSnapshot;
            }

            return [
                'id' => $partyId,
                'name' => (string)$partyId,
            ];
        }

        if (!empty($incomingSnapshot)) {
            $incomingSnapshot['id'] = empty($incomingSnapshot['id']) ? null : (int)$incomingSnapshot['id'];
            return $incomingSnapshot;
        }

        return [];
    }

    private function buildSftSnapshotById(?int $sftRecordId): array
    {
        if (empty($sftRecordId)) {
            return [];
        }

        $sftRecord = SftRecord::query()->find($sftRecordId);
        if (!$sftRecord) {
            return [];
        }

        return [
            'id' => (int)$sftRecord->id,
            'type' => (string)($sftRecord->type ?? ''),
            'name' => (string)($sftRecord->name ?? ''),
            'url' => (string)($sftRecord->url ?? ''),
            'code' => (string)($sftRecord->code ?? ''),
            'address' => (string)($sftRecord->address ?? ''),
            'country' => (string)($sftRecord->country ?? ''),
            'aeo_company_code' => (string)($sftRecord->aeo_company_code ?? ''),
            'contact_name' => (string)($sftRecord->contact_name ?? ''),
            'contact_phone' => (string)($sftRecord->contact_phone ?? ''),
            'phone' => (string)($sftRecord->phone ?? ''),
            'keyword' => (string)($sftRecord->keyword ?? ''),
        ];
    }

    private function normalizeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [];
            }
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (is_object($value)) {
            $decoded = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * 删除
     * @param Request $request
     * @param Order $order
     * @return Response
     * @throws InvalidRequestException
     */
    public function destroy(Request $request, Order $order): Response
    {
        $adminUser = $request->user();
        if ((int)$order->is_claimed === 1 && $adminUser->hasRole('商务')) {
            throw new InvalidRequestException('当前单据已被操作认领，禁止修改!');
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
        $adminUser = $request->user();
        $keyword = $request->input('keyword');
        $startSailingDate = $request->input('start_sailing_date');
        $endSailingDate = $request->input('end_sailing_date');
        $startArrivalDate = $request->input('start_arrival_date');
        $endArrivalDate = $request->input('end_arrival_date');
        $finishingDate = $request->input('finishing_date');
        $businessUserId = $request->input('business_user_id');
        $operateUserId = $request->input('operate_user_id');
        $isDelivery = $request->input('is_delivery');
        $paymentMethod = $request->input('payment_method');
        $sellerId = $request->input('seller_id');
        $isClaimed = $request->input('is_claimed');
        $pageSize = $request->input('page_size', 15);

        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'shippingCompany:id,name',
                'orderDelegationHeader',
            ])
            ->with('orderRemark', function ($query) use ($adminUser) {
                return $query->where('admin_user_id', $adminUser->id);
            })
            ->latest();

        if (!$adminUser->hasRole('超管')) {
            if ($adminUser->hasRole('商务')) {
                $builder = $builder->where('commerce_user_id', $adminUser->id);
            }
            if ($adminUser->hasRole('操作')) {
                $builder = $builder->where('operate_user_id', $adminUser->id)->where('is_claimed', 0);
            }
        }
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
        if (!empty($operateUserId)) {
            $builder = $builder->where('operate_user_id', $operateUserId);
        }
        if (!empty($isClaimed)) {
            $builder = $builder->where('is_claimed', 1);
        }
        if (!empty($isDelivery)) {
            $builder = $builder->where('is_delivery', $isDelivery);
        }
        $orders = $builder->paginate($pageSize);
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
        $pageSize = $request->input('page_size', 15);
        // 财务单据
        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'shippingCompany:id,name',
                'orderDelegationHeader',
            ])
            ->latest();

        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->where('job_no', 'like', '%' . $keyword . '%')
                    ->orWhere('origin_port', 'like', '%' . $keyword . '%')
                    ->orWhere('bl_no', 'like', '%' . $keyword . '%');
            });
        }

        $orders = $builder->paginate($pageSize);
        return FinanceOrderResource::collection($orders);
    }

    /**
     * 业务单据 - 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function businessIndex(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $keyword = $request->input('keyword');
        // 财务单据
        $builder = Order::query()
            ->with([
                'orderType:id,name',
                'businessUser:id,name',
                'operateUser:id,name',
                'documentUser:id,name',
                'commerceUser:id,name',
                'shippingCompany:id,name',
                'orderDelegationHeader:id,order_id,company_header_id,company_header_name',
            ])
            ->withCount('orderFiles')
            ->latest();

        if (!$adminUser->hasRole('超管') && $adminUser->hasRole('业务')) {
            $builder = $builder->where('business_user_id', $adminUser->id);
        }

        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->where('job_no', 'like', '%' . $keyword . '%')
                    ->orWhere('origin_port', 'like', '%' . $keyword . '%')
                    ->orWhere('bl_no', 'like', '%' . $keyword . '%');
            });
        }
        $orders = $builder->paginate();
        return BusinessOrderResource::collection($orders);
    }

    /**
     * 财务统计
     * @param Request $request
     * @return JsonResponse
     */
    public function financeStatistics(Request $request): JsonResponse
    {
        $builder = Order::query();
        $data = [
            'receipt_total_cny_amount' => $builder->clone()->sum('receipt_total_cny_amount'),
            'payment_total_cny_amount' => $builder->clone()->sum('payment_total_cny_amount'),
            'total_cny_gross_profit' => 0,
            'total_special_amount' => 0,
            'uncashed_amount' => 0,
            'cashed_amount' => 0,
            'receipt_total_usd_amount' => $builder->clone()->sum('receipt_total_usd_amount'),
            'payment_total_usd_amount' => $builder->clone()->sum('payment_total_usd_amount'),
            'total_usd_gross_profit' => 0,
            'total_gross_profit' => 0,
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
        if ((int)$order->payment_status === 1) {
            $order->payment_status = 0;
            $order->finish_at = null;
        } else {
            $order->payment_status = 1;
            $order->finish_at = Carbon::now();
        }
        $order->save();

        $finishAt = !empty($order->finish_at) ? Carbon::parse($order->finish_at)->format('Y-m-d') : '';

        return response()->json([
            'finish_at' => $finishAt
        ]);
    }


    /**
     * 筛选条件
     * @param Request $request
     * @return JsonResponse
     */
    public function filter(Request $request): JsonResponse
    {
        // 公司抬头
        $companyHeaders = CompanyHeader::query()->select(['id', 'company_name'])->get()->toArray();
        // 订单类型
        $orderTypes = OrderType::query()->get();
        // 业务员
        $role = Role::query()->where('code', 'BUSINESS')->first();
//        $businessUsers = AdminUser::query()
//            ->withWhereHas('roles', function ($query) use ($role) {
//                $query->where('id', $role->id);
//            })
//            ->get();
        // 操作员
//        $role = Role::query()
//            ->where('code', 'OPERATE')
//            ->first();
//        $operateUsers = AdminUser::query()
//            ->withWhereHas('roles', function ($query) use ($role) {
//                $query->where('id', $role->id);
//            })
//            ->get();
        $data = [
            'company_header' => $companyHeaders,
            'order_types' => $orderTypes,
//            'business_admin_users' => AdminUserResource::collection($businessUsers),
//            'operate_users' => $operateUsers
        ];
        return response()->json($data);
    }

    /**
     * 完成
     * @param Request $request
     * @param Order $order
     * @return Response
     */
    public function finish(Request $request, Order $order): Response
    {
        if ((int)$order->is_finish === 1) {
            $isFinish = 0;
        } else {
            $isFinish = 1;
        }
        $order->is_finish = $isFinish;
        $order->save();
        event(new OrderFinishEvent());
        return response()->noContent();
    }
}
