<?php
/**
 * 发票 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\Invoice\InvoiceInfoResource;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoicesController extends Controller
{
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
            // 兼容旧数据里的 price 字段，统一映射到 amount
            $item['amount'] = $this->normalizeDecimal($item['amount'] ?? ($item['price'] ?? 0));

            return $item;
        })->all();
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
                'order:id,job_no,is_finish',
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
        $invoiceTypeId = empty($request->input('invoice_type_id')) ? null : (int)$request->input('invoice_type_id');
        $email = $request->input('email');
        $remark = $request->input('remark');
        $invoiceDate = $request->input('invoice_date');
        $isFinish = $request->input('is_finish', 0);
        $commission = $request->input('commission');
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
        $cnyInvoiceItems = $request->input('cny_invoice_items');
        $usdInvoiceItems = $request->input('usd_invoice_items');

        // 如果单据完成 修改订单信息
        if ((int)$isFinish === 1) {
            Order::query()->where('id', $orderId)->update([
                'is_finish' => 1,
                'commission' => $commission,
            ]);
        }

        $invoice->order_id = $orderId;
        $invoice->invoice_type_id = $invoiceTypeId;
        $invoice->invoice_type_name = $this->resolveInvoiceTypeName($invoiceTypeId);
        $invoice->email = $email;
        $invoice->remark = $remark;
        $invoice->invoice_date = $invoiceDate;
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
        $invoice->save();

        $cnyInvoiceItems = $this->normalizeInvoiceItems($cnyInvoiceItems, 'cny');
        $usdInvoiceItems = $this->normalizeInvoiceItems($usdInvoiceItems, 'usd');
        $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);
        $invoiceItemRelation = [];
        foreach ($invoiceItems as $item) {
            $item['fee_type_name'] = $this->resolveFeeTypeName($item['fee_type_id'] ?? null);
            $invoiceItemRelation[] = new InvoiceItem($item);
        }
        $invoice->invoiceItems()->saveMany($invoiceItemRelation);
        return new InvoiceInfoResource($invoice->load([
            'cnyInvoiceItems',
            'usdInvoiceItems',
            'invoiceType:id,name,tax_rate,type,remark',
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
            $invoiceTypeId = empty($request->input('invoice_type_id')) ? null : (int)$request->input('invoice_type_id');
            $email = $request->input('email');
            $remark = $request->input('remark');
            $invoiceDate = $request->input('invoice_date');
            $isFinish = $request->input('is_finish', 0);
            $commission = $request->input('commission');
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
            $cnyInvoiceItems = $request->input('cny_invoice_items');
            $usdInvoiceItems = $request->input('usd_invoice_items');

            // 如果单据完成 修改订单信息
            if ((int)$isFinish === 1) {
                Order::query()->where('id', $orderId)->update([
                    'is_finish' => 1,
                    'commission' => $commission,
                ]);
            }

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
            $invoice->update();

            $cnyInvoiceItems = $this->normalizeInvoiceItems($cnyInvoiceItems, 'cny');
            $usdInvoiceItems = $this->normalizeInvoiceItems($usdInvoiceItems, 'usd');

            $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);

            $oldInvoiceItemIds = InvoiceItem::query()->where('invoice_id', $invoice->id)->pluck('id')->toArray();
            $newInvoiceItemIds = collect($invoiceItems)->pluck('id')->toArray();
            $deleteInvoiceItemIds = array_diff($oldInvoiceItemIds, $newInvoiceItemIds);
            InvoiceItem::destroy($deleteInvoiceItemIds);

            $invoiceItemRelation = [];

            $totalCnyAmount = 0;
            $totalUsdAmount = 0;

            foreach ($invoiceItems as $item) {
                $amount = (float)$this->normalizeDecimal($item['amount'] ?? 0);
                if ($item['currency'] === 'cny') {
                    $totalCnyAmount += $amount;
                }
                if ($item['currency'] === 'usd') {
                    $totalUsdAmount += $amount;
                }
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
            Log::info('发票人民币金额:' . $totalCnyAmount);
            Log::info('发票美金金额:' . $totalUsdAmount);
            $invoice->update([
                'total_cny_amount' => $totalCnyAmount,
                'total_usd_amount' => $totalUsdAmount,
            ]);
            return $invoice;
        });
        return new InvoiceInfoResource($invoice->load([
            'cnyInvoiceItems',
            'usdInvoiceItems',
            'invoiceType:id,name,tax_rate,type,remark',
        ]));
    }

    /**
     * 删除
     * @param Invoice $invoice
     * @return Response
     */
    public function destroy(Invoice $invoice): Response
    {
        $invoice->delete();
        return response()->noContent();
    }


    public function stat(Request $request)
    {
        dd('开票统计');
    }
}
