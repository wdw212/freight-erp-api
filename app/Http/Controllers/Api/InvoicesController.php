<?php
/**
 * 发票 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\Invoice\InvoiceInfoResource;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class InvoicesController extends Controller
{
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
                'invoiceType:id,name',
                'order:id,job_no',
                'order.orderDelegationHeader:id',
                'order.orderDelegationHeader.seller:id,name',
                'order.orderDelegationHeader.companyHeader:id,company_name',
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
        $invoiceTypeId = $request->input('invoice_type_id');
        $email = $request->input('email');
        $remark = $request->input('remark');
        $invoiceDate = $request->input('invoice_date');
        $isFinish = $request->input('is_finish', 0);
        $commission = $request->input('commission');
        $taxRate = $request->input('tax_rate');
        $taxAmount = $request->input('tax_amount');
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

        $cnyInvoiceItems = json_decode($cnyInvoiceItems, true);
        $usdInvoiceItems = json_decode($usdInvoiceItems, true);

        $cnyInvoiceItems = collect($cnyInvoiceItems)->map(function ($item) {
            $item['currency'] = 'cny';
            if (empty($item['fee_type_id'])) {
                $item['fee_type_id'] = null;
            }
            return $item;
        })->all();
        $usdInvoiceItems = collect($usdInvoiceItems)->map(function ($item) {
            $item['currency'] = 'usd';
            if (empty($item['fee_type_id'])) {
                $item['fee_type_id'] = null;
            }
            return $item;
        })->all();

        $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);

        $invoiceItemRelation = [];
        foreach ($invoiceItems as $item) {
            $invoiceItemRelation[] = new InvoiceItem($item);
        }
        $invoice->invoiceItems()->saveMany($invoiceItemRelation);
        return new InvoiceInfoResource($invoice->load(['cnyInvoiceItems', 'usdInvoiceItems']));
    }

    /**
     * 详情
     * @param Invoice $invoice
     * @return InvoiceInfoResource
     */
    public function show(Invoice $invoice): InvoiceInfoResource
    {
        return new InvoiceInfoResource($invoice->load(['cnyInvoiceItems', 'usdInvoiceItems']));
    }

    /**
     * 编辑
     * @param InvoiceRequest $request
     * @param Invoice $invoice
     * @return InvoiceInfoResource
     */
    public function update(InvoiceRequest $request, Invoice $invoice): InvoiceInfoResource
    {
        $orderId = $request->input('order_id');
        $invoiceTypeId = $request->input('invoice_type_id');
        $email = $request->input('email');
        $remark = $request->input('remark');
        $invoiceDate = $request->input('invoice_date');
        $isFinish = $request->input('is_finish', 0);
        $commission = $request->input('commission');
        $taxRate = $request->input('tax_rate');
        $taxAmount = $request->input('tax_amount');
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

        $cnyInvoiceItems = json_decode($cnyInvoiceItems, true);
        $usdInvoiceItems = json_decode($usdInvoiceItems, true);

        $cnyInvoiceItems = collect($cnyInvoiceItems)->map(function ($item) {
            $item['currency'] = 'cny';
            if (empty($item['fee_type_id'])) {
                $item['fee_type_id'] = null;
            }
            return $item;
        })->all();
        $usdInvoiceItems = collect($usdInvoiceItems)->map(function ($item) {
            $item['currency'] = 'usd';
            if (empty($item['fee_type_id'])) {
                $item['fee_type_id'] = null;
            }
            return $item;
        })->all();

        $invoiceItems = array_merge($cnyInvoiceItems, $usdInvoiceItems);

        $invoiceItemRelation = [];
        foreach ($invoiceItems as $item) {
            if (isset($item['id'])) {
                $invoiceItem = InvoiceItem::query()->where('id', $item['id'])->first();
                $invoiceItem->fill($item);
                $invoiceItem->update();
            } else {
                $invoiceItemRelation[] = new InvoiceItem($item);
            }
        }
        $invoice->invoiceItems()->saveMany($invoiceItemRelation);
        return new InvoiceInfoResource($invoice->load(['cnyInvoiceItems', 'usdInvoiceItems']));
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
}
