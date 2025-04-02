<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsdExchangeRateRequest;
use App\Http\Resources\UsdExchangeRate\UsdExchangeRateInfoResource;
use App\Http\Resources\UsdExchangeRate\UsdExchangeRateResource;
use App\Models\UsdExchangeRate;
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
}
