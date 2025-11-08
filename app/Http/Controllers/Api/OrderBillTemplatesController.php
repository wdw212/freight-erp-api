<?php
/**
 * 单据账单模版 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderBillTemplateRequest;
use App\Http\Resources\OrderBillTemplate\OrderBillTemplateInfoResource;
use App\Http\Resources\OrderBillTemplate\OrderBillTemplateResource;
use App\Models\OrderBillTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrderBillTemplatesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $orderBillTemplates = OrderBillTemplate::query()
            ->whereBelongsTo($adminUser)
            ->latest()
            ->get();
        OrderBillTemplateResource::wrap('data');
        return OrderBillTemplateResource::collection($orderBillTemplates);
    }

    /**
     * 新增
     * @param OrderBillTemplateRequest $request
     * @param OrderBillTemplate $orderBillTemplate
     * @return OrderBillTemplateInfoResource
     */
    public function store(OrderBillTemplateRequest $request, OrderBillTemplate $orderBillTemplate): OrderBillTemplateInfoResource
    {
        $adminUser = $request->user();
        $data = $request->all();

        if (!empty($data['order_bill_items'])) {
            $data['order_bill_items'] = json_decode($data['order_bill_items'], true);
        } else {
            $data['order_bill_items'] = [];
        }

        $orderBillTemplate->fill($data);
        $orderBillTemplate->adminUser()->associate($adminUser);
        $orderBillTemplate->save();

        return new OrderBillTemplateInfoResource($orderBillTemplate);
    }

    /**
     * 详情
     * @param OrderBillTemplate $orderBillTemplate
     * @return OrderBillTemplateInfoResource
     */
    public function show(OrderBillTemplate $orderBillTemplate): OrderBillTemplateInfoResource
    {
        return new OrderBillTemplateInfoResource($orderBillTemplate);
    }

    /**
     * 编辑
     * @param OrderBillTemplateRequest $request
     * @param OrderBillTemplate $orderBillTemplate
     * @return OrderBillTemplateInfoResource
     */
    public function update(OrderBillTemplateRequest $request, OrderBillTemplate $orderBillTemplate): OrderBillTemplateInfoResource
    {
        if (!empty($data['order_bill_items'])) {
            $data['order_bill_items'] = json_decode($data['order_bill_items'], true);
        } else {
            $data['order_bill_items'] = [];
        }

        $orderBillTemplate->fill($data);
        $orderBillTemplate->save();
        return new OrderBillTemplateInfoResource($orderBillTemplate);
    }

    /**
     * 删除
     * @param OrderBillTemplate $orderBillTemplate
     * @return Response
     */
    public function destroy(OrderBillTemplate $orderBillTemplate): Response
    {
        $orderBillTemplate->delete();
        return response()->noContent();
    }
}
