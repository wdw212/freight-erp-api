<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OperationFeeRequest;
use App\Http\Resources\OperationFee\OperationFeeInfoResource;
use App\Http\Resources\OperationFee\OperationFeeResource;
use App\Models\OperationFee;
use App\Models\OperationFeeItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class OperationFeesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $operationFees = OperationFee::query()->with([
            'operationFeeItems',
            'operationFeeItems.orderType'
        ])->orderByDesc('id')->paginate();
        return OperationFeeResource::collection($operationFees);
    }

    /**
     * 新增
     * @param OperationFeeRequest $request
     * @param OperationFee $operationFee
     * @return OperationFeeInfoResource
     * @throws Throwable
     */
    public function store(OperationFeeRequest $request, OperationFee $operationFee): OperationFeeInfoResource
    {
        $operationFee = DB::transaction(static function () use ($request, $operationFee) {
            $operationFee->fill($request->all());
            $operationFee->save();

            $items = json_decode($request->items, true);
            $itemRelations = [];
            foreach ($items as $item) {
                $itemRelations[] = new OperationFeeItem($item);
            }
            $operationFee->operationFeeItems()->saveMany($itemRelations);
            return $operationFee;
        });
        return new OperationFeeInfoResource($operationFee);
    }

    /**
     * 详情
     * @param OperationFee $operationFee
     * @return OperationFeeInfoResource
     */
    public function show(OperationFee $operationFee): OperationFeeInfoResource
    {
        return new OperationFeeInfoResource($operationFee);
    }

    /**
     * 编辑
     * @param OperationFeeRequest $request
     * @param OperationFee $operationFee
     * @return OperationFeeInfoResource
     * @throws Throwable
     */
    public function update(OperationFeeRequest $request, OperationFee $operationFee): OperationFeeInfoResource
    {
        $operationFee = DB::transaction(static function () use ($request, $operationFee) {
            $operationFee->fill($request->all());
            $operationFee->update();
            OperationFeeItem::query()->whereBelongsTo($operationFee)->delete();
            $items = json_decode($request->items, true);
            foreach ($items as $item) {
                $operationFeeItem = new OperationFeeItem($item);
                $operationFeeItem->operationFee()->associate($operationFee);
                $operationFeeItem->save();
            }
            return $operationFee;
        });
        return new OperationFeeInfoResource($operationFee);
    }
}
