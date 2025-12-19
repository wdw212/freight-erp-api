<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialCostRateRequest;
use App\Http\Resources\SpecialCostRate\SpecialCostRateInfoResource;
use App\Http\Resources\SpecialCostRate\SpecialCostRateResource;
use App\Models\SpecialCostRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SpecialCostRatesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $monthCode = $request->input('month_code', '');
        $builder = SpecialCostRate::query()
            ->orderByDesc('id');
        if (!empty($monthCode)) {
            $monthCode = Carbon::parse($monthCode)->format('Y-m');
            $builder = $builder->where('month_code', $monthCode);
        }
        $specialCostRates = $builder->paginate(15);
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
            throw new InvalidRequestException('汇率月份重复，选择该月份进行修改');
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
