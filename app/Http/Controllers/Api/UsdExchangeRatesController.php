<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsdExchangeRateRequest;
use App\Http\Resources\UsdExchangeRate\UsdExchangeRateInfoResource;
use App\Http\Resources\UsdExchangeRate\UsdExchangeRateResource;
use App\Models\UsdExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsdExchangeRatesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $usdExchangeRates = UsdExchangeRate::query()->orderByDesc('id')->paginate();
        return UsdExchangeRateResource::collection($usdExchangeRates);
    }

    /**
     * 新增
     * @param UsdExchangeRateRequest $request
     * @param UsdExchangeRate $usdExchangeRate
     * @return UsdExchangeRateInfoResource
     * @throws InvalidRequestException
     */
    public function store(UsdExchangeRateRequest $request, UsdExchangeRate $usdExchangeRate): UsdExchangeRateInfoResource
    {
        $monthCode = $request->input('month_code');
        if (UsdExchangeRate::query()->where('month_code', $monthCode)->exists()) {
            throw new InvalidRequestException('汇率月份重复，选择该月份进行修改');
        }
        $usdExchangeRate->fill($request->all());
        $usdExchangeRate->save();
        return new UsdExchangeRateInfoResource($usdExchangeRate);
    }

    /**
     * 详情
     * @param UsdExchangeRate $usdExchangeRate
     * @return UsdExchangeRateInfoResource
     */
    public function show(UsdExchangeRate $usdExchangeRate): UsdExchangeRateInfoResource
    {
        return new UsdExchangeRateInfoResource($usdExchangeRate);
    }

    /**
     * 编辑
     * @param Request $request
     * @param UsdExchangeRate $usdExchangeRate
     * @return UsdExchangeRateInfoResource
     * @throws InvalidRequestException
     */
    public function update(Request $request, UsdExchangeRate $usdExchangeRate): UsdExchangeRateInfoResource
    {
        $monthCode = $request->input('month_code');
        if (UsdExchangeRate::query()->where('month_code', $monthCode)->whereNot('id', $usdExchangeRate->id)->exists()) {
            throw new InvalidRequestException('汇率月份重复，选择该月份进行修改');
        }
        $usdExchangeRate->fill($request->all());
        $usdExchangeRate->update();
        return new UsdExchangeRateInfoResource($usdExchangeRate);
    }

    /**
     * 获取当月汇率（若无记录则自动从最近月份复制延用）
     * @param Request $request
     * @return UsdExchangeRateInfoResource|JsonResponse
     */
    public function getCurrentMonth(Request $request): UsdExchangeRateInfoResource|JsonResponse
    {
        $monthCode = $request->input('month_code')
            ? Carbon::parse($request->input('month_code'))->format('Y-m')
            : Carbon::now()->format('Y-m');

        $record = UsdExchangeRate::query()->where('month_code', $monthCode)->first();

        if (!$record) {
            $prevRecord = UsdExchangeRate::query()
                ->where('month_code', '<', $monthCode)
                ->orderByDesc('month_code')
                ->first();

            if (!$prevRecord) {
                return response()->json(['data' => null, 'message' => '暂无历史汇率可延用']);
            }

            $record = $prevRecord->replicate();
            $record->month_code = $monthCode;
            $record->save();
        }

        return new UsdExchangeRateInfoResource($record);
    }
}
