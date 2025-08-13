<?php
/**
 * 费用类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeeTypeRequest;
use App\Http\Resources\FeeType\FeeTypeInfoResource;
use App\Http\Resources\FeeType\FeeTypeResource;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FeeTypesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $feeTypes = FeeType::query()->orderByDesc('sort')->paginate();
        return FeeTypeResource::collection($feeTypes);
    }

    /**
     * 新增
     * @param FeeTypeRequest $request
     * @param FeeType $feeType
     * @return FeeTypeInfoResource
     * @throws InvalidRequestException
     */
    public function store(FeeTypeRequest $request, FeeType $feeType): FeeTypeInfoResource
    {
        $name = $request->input('name');

        if (FeeType::query()->where('name', $name)->exists()) {
            throw new InvalidRequestException('已存在，请重试！');
        }

        $feeType->fill($request->all());
        $feeType->save();
        return new FeeTypeInfoResource($feeType);
    }

    /**
     * 编辑
     * @param FeeTypeRequest $request
     * @param FeeType $feeType
     * @return FeeTypeInfoResource
     * @throws InvalidRequestException
     */
    public function update(FeeTypeRequest $request, FeeType $feeType): FeeTypeInfoResource
    {
        $name = $request->input('name');

        if (FeeType::query()->whereNot('id', $feeType->id)->where('name', $name)->exists()) {
            throw new InvalidRequestException('已存在，请重试');
        }

        $feeType->fill($request->all());
        $feeType->update();
        return new FeeTypeInfoResource($feeType);
    }

    /**
     * 删除
     * @param FeeType $feeType
     * @return Response
     */
    public function destroy(FeeType $feeType): Response
    {
        $feeType->delete();
        return response()->noContent();
    }
}
