<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialTaxRateRequest;
use App\Http\Resources\SpecialTaxRate\SpecialTaxRateInfoResource;
use App\Http\Resources\SpecialTaxRate\SpecialTaxRateResource;
use App\Models\SpecialTaxRate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpecialTaxRatesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $specialTaxRates = SpecialTaxRate::query()->orderByDesc('id')->paginate();
        return SpecialTaxRateResource::collection($specialTaxRates);
    }

    /**
     * 新增
     * @param SpecialTaxRateRequest $request
     * @param SpecialTaxRate $specialTaxRate
     * @return SpecialTaxRateInfoResource
     * @throws InvalidRequestException
     */
    public function store(SpecialTaxRateRequest $request, SpecialTaxRate $specialTaxRate): SpecialTaxRateInfoResource
    {
        $monthCode = $request->input('month_code');
        if (SpecialTaxRate::query()->where('month_code', $monthCode)->exists()) {
            throw new InvalidRequestException('汇率月份重复，选择该月份进行修改');
        }
        $specialTaxRate->fill($request->all());
        $specialTaxRate->save();
        return new SpecialTaxRateInfoResource($specialTaxRate);
    }

    /**
     * 详情
     * @param SpecialTaxRate $specialTaxRate
     * @return SpecialTaxRateInfoResource
     */
    public function show(SpecialTaxRate $specialTaxRate): SpecialTaxRateInfoResource
    {
        return new SpecialTaxRateInfoResource($specialTaxRate);
    }

    /**
     * 新增
     * @param SpecialTaxRateRequest $request
     * @param SpecialTaxRate $specialTaxRate
     * @return SpecialTaxRateInfoResource
     */
    public function update(SpecialTaxRateRequest $request, SpecialTaxRate $specialTaxRate): SpecialTaxRateInfoResource
    {
        $specialTaxRate->fill($request->all());
        $specialTaxRate->update();
        return new SpecialTaxRateInfoResource($specialTaxRate);
    }

    /**
     * 获取当月参数（若无记录则自动从最近月份复制延用）
     * @param Request $request
     * @return SpecialTaxRateInfoResource|JsonResponse
     */
    public function getCurrentMonth(Request $request): SpecialTaxRateInfoResource|JsonResponse
    {
        $monthCode = $request->input('month_code')
            ? Carbon::parse($request->input('month_code'))->format('Y-m')
            : Carbon::now()->format('Y-m');

        $record = SpecialTaxRate::query()->where('month_code', $monthCode)->first();

        if (!$record) {
            $prevRecord = SpecialTaxRate::query()
                ->where('month_code', '<', $monthCode)
                ->orderByDesc('month_code')
                ->first();

            if (!$prevRecord) {
                return response()->json(['data' => null, 'message' => '暂无历史参数可延用']);
            }

            $record = $prevRecord->replicate();
            $record->month_code = $monthCode;
            $record->save();
        }

        return new SpecialTaxRateInfoResource($record);
    }
}
