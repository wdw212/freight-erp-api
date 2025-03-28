<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialCostRateRequest;
use App\Http\Resources\SpecialCostRate\SpecialCostRateInfoResource;
use App\Http\Resources\SpecialCostRate\SpecialCostRateResource;
use App\Models\SpecialCostRate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SpecialCostRatesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $specialCostRates = SpecialCostRate::query()->orderByDesc('id')->paginate(15);
        return SpecialCostRateResource::collection($specialCostRates);
    }

    /**
     * 新增
     * @param SpecialCostRateRequest $request
     * @param SpecialCostRate $specialCostRate
     * @return SpecialCostRateInfoResource
     * @throws InvalidRequestException
     */
    public function store(SpecialCostRateRequest $request, SpecialCostRate $specialCostRate): SpecialCostRateInfoResource
    {
        $monthCode = $request->input('month_code');
        if (SpecialCostRate::query()->where('month_code', $monthCode)->exists()) {
            throw new InvalidRequestException('月份已存在，请重试！');
        }
        $specialCostRate->fill($request->all());
        $specialCostRate->save();
        return new SpecialCostRateInfoResource($specialCostRate);
    }

    /**
     * 详情
     * @param SpecialCostRate $specialCostRate
     * @return SpecialCostRateInfoResource
     */
    public function show(SpecialCostRate $specialCostRate): SpecialCostRateInfoResource
    {
        return new SpecialCostRateInfoResource($specialCostRate);
    }

    /**
     * 编辑
     * @param SpecialCostRateRequest $request
     * @param SpecialCostRate $specialCostRate
     * @return SpecialCostRateInfoResource
     */
    public function update(SpecialCostRateRequest $request, SpecialCostRate $specialCostRate): SpecialCostRateInfoResource
    {
        $specialCostRate->fill($request->all());
        $specialCostRate->update();
        return new SpecialCostRateInfoResource($specialCostRate);
    }

    /**
     * 删除
     * @param SpecialCostRate $specialCostRate
     * @return Response
     */
    public function destroy(SpecialCostRate $specialCostRate): Response
    {
        $specialCostRate->delete();
        return response()->noContent();
    }
}
