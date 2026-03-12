<?php
/**
 * 发票 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\Invoice\InvoiceInfoResource;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\Order;
use App\Models\OrderReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoicesController extends Controller
{
    private function currentRoleCodes(Request $request): array
    {
        $adminUser = $request->user();
        if (!$adminUser) {
            return [];
        }

        return $adminUser->roles()->pluck('code')->toArray();
    }

    private function isSuperAdmin(Request $request): bool
    {
        return in_array('SUPER_ADMIN', $this->currentRoleCodes($request), true);
    }

    private function normalizeInvoiceNumber(mixed $value): string
    {
        return trim((string)($value ?? ''));
    }

    private function shouldConfirmInvoice(Request $request): bool
    {
        return (int)$request->input('confirm_invoice', 0) === 1;
    }

    private function ensureConfirmRequiresInvoiceNumber(bool $shouldConfirm, string $cnyInvoiceNo, string $usdInvoiceNo): void
    {
        if (!$shouldConfirm) {
            return;
        }

        if ($cnyInvoiceNo === '' && $usdInvoiceNo === '') {
            throw new InvalidRequestException('确认开票前请先填写发票号');
        }
    }

    private function syncOrderInvoiceLockStatus(mixed $orderId): void
    {
        if (empty($orderId)) {
            return;
        }

        $hasLockedInvoice = Invoice::query()
            ->where('order_id', $orderId)
            ->where(function ($query) {
                $query->where(function ($invoiceQuery) {
                    $invoiceQuery->whereNotNull('cny_invoice_no')
                        ->where('cny_invoice_no', '<>', '');
                })->orWhere(function ($invoiceQuery) {
                    $invoiceQuery->whereNotNull('usd_invoice_no')
                        ->where('usd_invoice_no', '<>', '');
                });
            })
            ->exists();

        Order::query()->where('id', $orderId)->update([
            'is_lock' => $hasLockedInvoice ? 1 : 0,
        ]);
    }

    private function syncOrderSpecialFeeFromConfirmedInvoices(mixed $orderId): void
    {
        if (empty($orderId)) {
            return;
        }

        $specialFee = Invoice::query()
            ->where('order_id', $orderId)
            ->whereNotNull('confirm_at')
            ->where('is_finish', 1)
            ->sum('total_cny_amount');

        Order::query()->where('id', $orderId)->update([
            'special_fee' => number_format((float)$specialFee, 2, '.', ''),
        ]);
    }

    private function syncConfirmedOrderReceipt(Invoice $invoice): void
    {
        if (empty($invoice->confirm_at) || empty($invoice->order_id)) {
            return;
        }

        $companyHeaderId = empty($invoice->purchase_entity_id) ? null : (int)$invoice->purchase_entity_id;
        $companyHeaderName = trim((string)($invoice->purchase_entity['name'] ?? ''));

        $orderReceipt = OrderReceipt::query()
            ->where('order_id', $invoice->order_id)
            ->when($companyHeaderId !== null, static function ($query) use ($companyHeaderId) {
                $query->where('company_header_id', $companyHeaderId);
            })
            ->where('cny_amount', $invoice->total_cny_amount)
            ->where('usd_amount', $invoice->total_usd_amount)
            ->orderByDesc('id')
            ->first();

        if (!$orderReceipt) {
            $orderReceipt = new OrderReceipt();
            $orderReceipt->order_id = $invoice->order_id;
        }

        $orderReceipt->company_header_id = $companyHeaderId;
        $orderReceipt->company_header_name = $companyHeaderName;
        $orderReceipt->cny_amount = $invoice->total_cny_amount;
        $orderReceipt->usd_amount = $invoice->total_usd_amount;
        $orderReceipt->cny_invoice_number = $invoice->cny_invoice_no;
        $orderReceipt->usd_invoice_number = $invoice->usd_invoice_no;
        $orderReceipt->save();
    }

    /**
     * 同步发票页“单子完结”到订单；订单不存在时只保留发票快照
     *
     * @param mixed $orderId
     * @param int $isFinish
     * @param string $commission
     * @return void
     */
    private function syncOrderFinishStatus(mixed $orderId, int $isFinish, string $commission): void
    {
        if (empty($orderId)) {
            return;
        }

        Order::query()->where('id', $orderId)->update([
            'is_finish' => $isFinish,
            'commission' => $commission,
        ]);
    }

    /**
     * 归一化小数输入，避免空字符串写入 decimal 字段导致 SQL 异常
     *
     * @param mixed $value
     * @return string
     */
    private function normalizeDecimal(mixed $value): string
    {
        if ($value === null) {
            return '0';
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return '0';
            }
            if (str_ends_with($value, '%')) {
                $value = rtrim($value, '%');
            }
        }

        return is_numeric($value) ? (string)$value : '0';
    }

    /**
     * 归一化可空整数字段，避免空字符串写入 integer/bigint 字段导致 SQL 异常
     *
     * @param mixed $value
     * @return int|null
     */
    private function normalizeNullableInteger(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
        }

        if (is_int($value)) {
            return $value >= 0 ? $value : null;
        }

        if (is_string($value) && preg_match('/^\d+$/', $value) === 1) {
            return (int)$value;
        }

        return null;
    }

    /**
     * 归一化开票明细，兼容旧模板字段并兜底缺省值
     *
     * @param string|array|null $invoiceItems
     * @param string $currency
     * @return array
     */
    private function normalizeInvoiceItems(string|array|null $invoiceItems, string $currency): array
    {
        if (is_string($invoiceItems)) {
            $invoiceItems = json_decode($invoiceItems, true);
        }

        if (!is_array($invoiceItems)) {
            return [];
        }

        return collect($invoiceItems)->map(function ($item) use ($currency) {
            if (!is_array($item)) {
                $item = [];
            }

            $item['currency'] = $currency;
            $item['fee_type_id'] = empty($item['fee_type_id']) ? null : (int)$item['fee_type_id'];
            $item['unit'] = $this->normalizeNullableInteger($item['unit'] ?? null);
            $item['quantity'] = $this->normalizeNullableInteger($item['quantity'] ?? null);
            // 兼容旧数据里的 price 字段，统一映射到 amount
            $item['amount'] = $this->normalizeDecimal($item['amount'] ?? ($item['price'] ?? 0));
            $item['fee_type_name'] = trim((string)($item['fee_type_name'] ?? ''));

            return $item;
        })->all();
    }

    /**
     * 汇总发票明细金额，确保保存发票主体前已有总额
     *
     * @param array $invoiceItems
     * @return array{cny: string, usd: string}
     */
    private function summarizeInvoiceItemAmounts(array $invoiceItems): array
    {
        $totalCnyAmount = 0.0;
        $totalUsdAmount = 0.0;

        foreach ($invoiceItems as $item) {
            $amount = (float)$this->normalizeDecimal($item['amount'] ?? 0);
            if (($item['currency'] ?? '') === 'cny') {
                $totalCnyAmount += $amount;
            }
            if (($item['currency'] ?? '') === 'usd') {
                $totalUsdAmount += $amount;
            }
        }

        return [
            'cny' => number_format($totalCnyAmount, 2, '.', ''),
            'usd' => number_format($totalUsdAmount, 2, '.', ''),
        ];
    }

    /**
     * 解析发票类型快照名称
     * @param int|null $invoiceTypeId
     * @return string
     */
    private function resolveInvoiceTypeName(?int $invoiceTypeId): string
    {
        if (empty($invoiceTypeId)) {
            return '';
        }
        return InvoiceType::query()->find($invoiceTypeId)?->name ?? '';
    }

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
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $orderId = $request->input('order_id');

        $builder = Invoice::query()
            ->with([
                'invoiceType:id,name,tax_rate,type,remark',
                'order:id,job_no,is_finish,commission,is_lock',
            ])
            ->latest();

        if (isset($orderId)) {
            $builder = $builder->where('order_id', $orderId);
        }

        $invoices = $builder->paginate();
        return InvoiceResource::collection($invoices);
    }

    /**
     * 新增
     * @param InvoiceRequest $request
     * @param Invoice $invoice
     * @return InvoiceInfoResource
     */
    public function store(InvoiceRequest $request, Invoice $invoice): InvoiceInfoResource
    {
        $orderId = $request->input('order_id');
        $invoiceTypeId = (int)$request->input('invoice_type_id');
        $email = $request->input('email');
        $remark = $request->input('remark');
        $invoiceDate = $request->input('invoice_date');
        $isFinish = (int)$request->input('is_finish', 0);
        $commission = $this->normalizeDecimal($request->input('commission'));
        $taxRate = $this->normalizeDecimal($request->input('tax_rate'));
        $taxAmount = $this->normalizeDecimal($request->input('tax_amount'));
        $cnyInvoiceNo = $request->input('cny_invoice_no');
        $usdInvoiceNo = $request->input('usd_invoice_no');
        $cnyRemark = $request->input('cny_remark');
        $usdRemark = $request->input('usd_remark');
        $purchaseEntityId = $request->input('purchase_entity_id');
        $purchaseUscCode = $request->input('purchase_usc_code');
        $saleEntityId = $request->input('sale_entity_id');
        $saleUscCode = $request->input('sale_usc_code');
        $cnyInvoiceItems = $this->normalizeInvoiceItems($request->input('cny_invoice_items'), 'cny');
        $usdInvoiceItems = $this->normalizeInvoiceItems($request->input('usd_invoice_items'), 'usd');
        $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);
        $amountSummary = $this->summarizeInvoiceItemAmounts($invoiceItems);

        $cnyInvoiceNo = $this->normalizeInvoiceNumber($cnyInvoiceNo);
        $usdInvoiceNo = $this->normalizeInvoiceNumber($usdInvoiceNo);
        $shouldConfirm = $this->shouldConfirmInvoice($request);
        $this->ensureConfirmRequiresInvoiceNumber($shouldConfirm, $cnyInvoiceNo, $usdInvoiceNo);

        $this->syncOrderFinishStatus($orderId, $isFinish, $commission);

        $invoice->order_id = $orderId;
        $invoice->invoice_type_id = $invoiceTypeId;
        $invoice->invoice_type_name = $this->resolveInvoiceTypeName($invoiceTypeId);
        $invoice->email = $email;
        $invoice->remark = $remark;
        $invoice->invoice_date = $invoiceDate;
        $invoice->is_finish = $isFinish;
        $invoice->commission = $commission;
        $invoice->tax_rate = $taxRate;
        $invoice->tax_amount = $taxAmount;
        $invoice->cny_invoice_no = $cnyInvoiceNo;
        $invoice->usd_invoice_no = $usdInvoiceNo;
        $invoice->cny_remark = $cnyRemark;
        $invoice->usd_remark = $usdRemark;
        $invoice->purchase_entity_id = $purchaseEntityId;
        $invoice->purchase_usc_code = $purchaseUscCode;
        $invoice->sale_entity_id = $saleEntityId;
        $invoice->sale_usc_code = $saleUscCode;
        $invoice->confirm_at = $shouldConfirm ? Carbon::now() : null;
        $invoice->total_cny_amount = $amountSummary['cny'];
        $invoice->total_usd_amount = $amountSummary['usd'];
        $invoice->save();

        $invoiceItemRelation = [];
        foreach ($invoiceItems as $item) {
            $item['fee_type_name'] = $this->resolveFeeTypeName($item['fee_type_id'] ?? null);
            $invoiceItemRelation[] = new InvoiceItem($item);
        }
        $invoice->invoiceItems()->saveMany($invoiceItemRelation);
        $invoice->refresh();
        $this->syncOrderInvoiceLockStatus($orderId);
        $this->syncConfirmedOrderReceipt($invoice);
        $this->syncOrderSpecialFeeFromConfirmedInvoices($orderId);
        return new InvoiceInfoResource($invoice->load([
            'cnyInvoiceItems',
            'usdInvoiceItems',
            'invoiceType:id,name,tax_rate,type,remark',
            'order:id,job_no,is_finish,commission,is_lock,special_fee',
        ]));
    }

    /**
     * 详情
     * @param Invoice $invoice
     * @return InvoiceInfoResource
     */
    public function show(Invoice $invoice): InvoiceInfoResource
    {
        return new InvoiceInfoResource($invoice->load([
            'cnyInvoiceItems',
            'usdInvoiceItems',
            'invoiceType:id,name,tax_rate,type,remark',
            'order:id,job_no,is_finish,commission,is_lock,special_fee',
        ]));
    }

    /**
     * 编辑
     * @param InvoiceRequest $request
     * @param Invoice $invoice
     * @return InvoiceInfoResource
     * @throws Throwable
     */
    public function update(InvoiceRequest $request, Invoice $invoice): InvoiceInfoResource
    {
        $invoice = DB::transaction(function () use ($request, $invoice) {
            $orderId = $request->input('order_id');
            $invoiceTypeId = (int)$request->input('invoice_type_id');
            $email = $request->input('email');
            $remark = $request->input('remark');
            $invoiceDate = $request->input('invoice_date');
            $isFinish = (int)$request->input('is_finish', 0);
            $commission = $this->normalizeDecimal($request->input('commission'));
            $taxRate = $this->normalizeDecimal($request->input('tax_rate'));
            $taxAmount = $this->normalizeDecimal($request->input('tax_amount'));
            $cnyInvoiceNo = $request->input('cny_invoice_no');
            $usdInvoiceNo = $request->input('usd_invoice_no');
            $cnyRemark = $request->input('cny_remark');
            $usdRemark = $request->input('usd_remark');
            $purchaseEntityId = $request->input('purchase_entity_id');
            $purchaseUscCode = $request->input('purchase_usc_code');
            $saleEntityId = $request->input('sale_entity_id');
            $saleUscCode = $request->input('sale_usc_code');
            $cnyInvoiceItems = $this->normalizeInvoiceItems($request->input('cny_invoice_items'), 'cny');
            $usdInvoiceItems = $this->normalizeInvoiceItems($request->input('usd_invoice_items'), 'usd');
            $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);
            $amountSummary = $this->summarizeInvoiceItemAmounts($invoiceItems);

            $isSuperAdmin = $this->isSuperAdmin($request);
            $cnyInvoiceNo = $this->normalizeInvoiceNumber($cnyInvoiceNo);
            $usdInvoiceNo = $this->normalizeInvoiceNumber($usdInvoiceNo);
            $isAlreadyConfirmed = !empty($invoice->confirm_at);
            $shouldConfirm = $isAlreadyConfirmed || $this->shouldConfirmInvoice($request);
            if ($isAlreadyConfirmed && !$isSuperAdmin) {
                $cnyInvoiceNo = $this->normalizeInvoiceNumber($invoice->cny_invoice_no);
                $usdInvoiceNo = $this->normalizeInvoiceNumber($invoice->usd_invoice_no);
            }
            $this->ensureConfirmRequiresInvoiceNumber($shouldConfirm, $cnyInvoiceNo, $usdInvoiceNo);

            $this->syncOrderFinishStatus($orderId, $isFinish, $commission);

            $originalInvoiceTypeId = (int)($invoice->invoice_type_id ?? 0);
            $originalInvoiceTypeName = (string)($invoice->invoice_type_name ?? '');

            $invoice->order_id = $orderId;
            $invoice->invoice_type_id = $invoiceTypeId;
            if (!empty($invoiceTypeId)) {
                $invoiceTypeChanged = (int)$invoiceTypeId !== $originalInvoiceTypeId;
                $invoice->invoice_type_name = ($invoiceTypeChanged || empty($originalInvoiceTypeName))
                    ? $this->resolveInvoiceTypeName($invoiceTypeId)
                    : $originalInvoiceTypeName;
            } else {
                $invoice->invoice_type_name = '';
            }
            $invoice->email = $email;
            $invoice->remark = $remark;
            $invoice->invoice_date = $invoiceDate;
            $invoice->is_finish = $isFinish;
            $invoice->commission = $commission;
            $invoice->tax_rate = $taxRate;
            $invoice->tax_amount = $taxAmount;
            $invoice->cny_invoice_no = $cnyInvoiceNo;
            $invoice->usd_invoice_no = $usdInvoiceNo;
            $invoice->cny_remark = $cnyRemark;
            $invoice->usd_remark = $usdRemark;
            $invoice->purchase_entity_id = $purchaseEntityId;
            $invoice->purchase_usc_code = $purchaseUscCode;
            $invoice->sale_entity_id = $saleEntityId;
            $invoice->sale_usc_code = $saleUscCode;
            $invoice->confirm_at = $isAlreadyConfirmed
                ? $invoice->confirm_at
                : ($shouldConfirm ? Carbon::now() : null);
            $invoice->total_cny_amount = $amountSummary['cny'];
            $invoice->total_usd_amount = $amountSummary['usd'];
            $invoice->update();

            $oldInvoiceItemIds = InvoiceItem::query()->where('invoice_id', $invoice->id)->pluck('id')->toArray();
            $newInvoiceItemIds = collect($invoiceItems)->pluck('id')->toArray();
            $deleteInvoiceItemIds = array_diff($oldInvoiceItemIds, $newInvoiceItemIds);
            InvoiceItem::destroy($deleteInvoiceItemIds);

            $invoiceItemRelation = [];

            foreach ($invoiceItems as $item) {
                $item['fee_type_id'] = empty($item['fee_type_id']) ? null : (int)$item['fee_type_id'];
                if (isset($item['id'])) {
                    $invoiceItem = InvoiceItem::query()->where('id', $item['id'])->first();
                    if (!$invoiceItem) {
                        continue;
                    }
                    $originalFeeTypeId = (int)($invoiceItem->fee_type_id ?? 0);
                    $originalFeeTypeName = (string)($invoiceItem->fee_type_name ?? '');
                    if (!empty($item['fee_type_id'])) {
                        $feeTypeChanged = (int)$item['fee_type_id'] !== $originalFeeTypeId;
                        $item['fee_type_name'] = ($feeTypeChanged || empty($originalFeeTypeName))
                            ? $this->resolveFeeTypeName($item['fee_type_id'])
                            : $originalFeeTypeName;
                    } else {
                        $item['fee_type_name'] = '';
                    }
                    $invoiceItem->fill($item);
                    $invoiceItem->update();
                } else {
                    $item['fee_type_name'] = $this->resolveFeeTypeName($item['fee_type_id'] ?? null);
                    $invoiceItemRelation[] = new InvoiceItem($item);
                }
            }
            $invoice->invoiceItems()->saveMany($invoiceItemRelation);
            Log::info('发票人民币金额:' . $amountSummary['cny']);
            Log::info('发票美金金额:' . $amountSummary['usd']);
            $invoice->refresh();
            $this->syncOrderInvoiceLockStatus($orderId);
            $this->syncConfirmedOrderReceipt($invoice);
            $this->syncOrderSpecialFeeFromConfirmedInvoices($orderId);
            return $invoice;
        });
        return new InvoiceInfoResource($invoice->load([
            'cnyInvoiceItems',
            'usdInvoiceItems',
            'invoiceType:id,name,tax_rate,type,remark',
            'order:id,job_no,is_finish,commission,is_lock,special_fee',
        ]));
    }

    /**
     * 删除
     * 仅超管允许删除；财务角色在开票管理中保持只读
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return Response
     * @throws Throwable
     */
    public function destroy(Request $request, Invoice $invoice): Response
    {
        $adminUser = $request->user();
        $roleCodes = $adminUser->roles()->pluck('code')->toArray();
        if (!in_array('SUPER_ADMIN', $roleCodes, true)) {
            throw new InvalidRequestException('仅超管可以删除开票管理信息', 403);
        }

        DB::transaction(function () use ($invoice) {
            $invoice->delete();
        });

        return response()->noContent();
    }


    public function stat(Request $request)
    {
        dd('开票统计');
    }
}
