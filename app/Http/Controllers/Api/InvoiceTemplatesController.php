<?php
/**
 * 发票模版 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceTemplateRequest;
use App\Http\Resources\InvoiceTemplate\InvoiceTemplateInfoResource;
use App\Http\Resources\InvoiceTemplate\InvoiceTemplateResource;
use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class InvoiceTemplatesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $invoiceTemplates = InvoiceTemplate::query()
            ->whereBelongsTo($adminUser)
            ->latest()
            ->get();
        InvoiceTemplateResource::wrap('data');
        return InvoiceTemplateResource::collection($invoiceTemplates);
    }

    /**
     * 新增
     * @param InvoiceTemplateRequest $request
     * @param InvoiceTemplate $invoiceTemplate
     * @return InvoiceTemplateInfoResource
     */
    public function store(InvoiceTemplateRequest $request, InvoiceTemplate $invoiceTemplate): InvoiceTemplateInfoResource
    {
        $adminUser = $request->user();

        $data = $request->all();

        if (!empty($data['cny_invoice_items'])) {
            $data['cny_invoice_items'] = json_decode($data['cny_invoice_items'], true);
        } else {
            $data['cny_invoice_items'] = [];
        }

        if (!empty($data['usd_invoice_items'])) {
            $data['usd_invoice_items'] = json_decode($data['usd_invoice_items'], true);
        } else {
            $data['usd_invoice_items'] = [];
        }

        $invoiceTemplate->fill($data);
        $invoiceTemplate->adminUser()->associate($adminUser);
        $invoiceTemplate->save();
        return new InvoiceTemplateInfoResource($invoiceTemplate);
    }

    /**
     * 详情
     * @param InvoiceTemplate $invoiceTemplate
     * @return InvoiceTemplateInfoResource
     */
    public function show(InvoiceTemplate $invoiceTemplate): InvoiceTemplateInfoResource
    {
        return new InvoiceTemplateInfoResource($invoiceTemplate);
    }

    public function update(InvoiceTemplateRequest $request, InvoiceTemplate $invoiceTemplate)
    {
        $data = $request->all();

        if (!empty($data['cny_invoice_items'])) {
            $data['cny_invoice_items'] = json_decode($data['cny_invoice_items'], true);
        } else {
            $data['cny_invoice_items'] = [];
        }

        if (!empty($data['usd_invoice_items'])) {
            $data['usd_invoice_items'] = json_decode($data['usd_invoice_items'], true);
        } else {
            $data['usd_invoice_items'] = [];
        }

        $invoiceTemplate->fill($data);
        $invoiceTemplate->update();
        return new InvoiceTemplateInfoResource($invoiceTemplate);
    }

    /**
     * 删除
     * @param InvoiceTemplate $invoiceTemplate
     * @return Response
     */
    public function destroy(InvoiceTemplate $invoiceTemplate): Response
    {
        $invoiceTemplate->delete();
        return response()->noContent();
    }
}
