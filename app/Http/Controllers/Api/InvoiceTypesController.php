<?php
/**
 * 发票类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceTypeRequest;
use App\Http\Resources\InvoiceType\InvoiceTypeInfoResource;
use App\Http\Resources\InvoiceType\InvoiceTypeResource;
use App\Models\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class InvoiceTypesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $FeeTypes = InvoiceType::query()->orderByDesc('sort')->get();
        InvoiceTypeResource::wrap('data');
        return InvoiceTypeResource::collection($FeeTypes);
    }

    /**
     * 新增
     * @param InvoiceTypeRequest $request
     * @param InvoiceType $invoiceType
     * @return InvoiceTypeInfoResource
     */
    public function store(InvoiceTypeRequest $request, InvoiceType $invoiceType): InvoiceTypeInfoResource
    {
        $invoiceType->fill($request->all());
        $invoiceType->save();
        return new InvoiceTypeInfoResource($invoiceType);
    }

    /**
     * 编辑
     * @param InvoiceTypeRequest $request
     * @param InvoiceType $invoiceType
     * @return InvoiceTypeInfoResource
     */
    public function update(InvoiceTypeRequest $request, InvoiceType $invoiceType): InvoiceTypeInfoResource
    {
        $invoiceType->fill($request->all());
        $invoiceType->update();
        return new InvoiceTypeInfoResource($invoiceType);
    }

    /**
     * 删除
     * @param InvoiceType $invoiceType
     * @return Response
     */
    public function destroy(InvoiceType $invoiceType): Response
    {
        $invoiceType->delete();
        return response()->noContent();
    }
}
