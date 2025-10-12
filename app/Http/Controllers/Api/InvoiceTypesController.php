<?php
/**
 * 发票类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
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
     * @throws InvalidRequestException
     */
    public function store(InvoiceTypeRequest $request, InvoiceType $invoiceType): InvoiceTypeInfoResource
    {
        $name = $request->input('name');

        if (InvoiceType::query()->where('name', $name)->exists()) {
            throw new InvalidRequestException('已存在，请重试!');
        }

        $invoiceType->fill($request->all());
        $invoiceType->save();
        return new InvoiceTypeInfoResource($invoiceType);
    }

    /**
     * 详情
     * @param InvoiceType $invoiceType
     * @return InvoiceTypeInfoResource
     */
    public function show(InvoiceType $invoiceType): InvoiceTypeInfoResource
    {
        return new InvoiceTypeInfoResource($invoiceType);
    }

    /**
     * 编辑
     * @param InvoiceTypeRequest $request
     * @param InvoiceType $invoiceType
     * @return InvoiceTypeInfoResource
     * @throws InvalidRequestException
     */
    public function update(InvoiceTypeRequest $request, InvoiceType $invoiceType): InvoiceTypeInfoResource
    {
        $name = $request->input('name');

        if (InvoiceType::query()->whereNot('id', $invoiceType->id)->where('name', $name)->exists()) {
            throw new InvalidRequestException('已存在，请重试!');
        }

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
