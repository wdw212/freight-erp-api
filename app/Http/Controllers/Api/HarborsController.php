<?php
/**
 * 港口 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HarborRequest;
use App\Http\Resources\Api\Harbor\HarborInfoResource;
use App\Http\Resources\Api\Harbor\HarborResource;
use App\Models\Harbor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class HarborsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $harbors = Harbor::query()->latest()->paginate();
        return HarborResource::collection($harbors);
    }

    /**
     * 新增
     * @param HarborRequest $request
     * @param Harbor $harbor
     * @return HarborInfoResource
     * @throws InvalidRequestException
     */
    public function store(HarborRequest $request, Harbor $harbor): HarborInfoResource
    {
        $data = $request->validated();
        if (Harbor::query()->where('code', $data['code'])->exists()) {
            throw new InvalidRequestException('港口已存在，请重试！');
        }
        $harbor->fill($data);
        $harbor->save();
        return new HarborInfoResource($harbor);
    }

    /**
     * 详情
     * @param Harbor $harbor
     * @return HarborInfoResource
     */
    public function show(Harbor $harbor): HarborInfoResource
    {
        return new HarborInfoResource($harbor);
    }

    /**
     * 编辑
     * @param HarborRequest $request
     * @param Harbor $harbor
     * @return HarborInfoResource
     * @throws InvalidRequestException
     */
    public function update(HarborRequest $request, Harbor $harbor): HarborInfoResource
    {
        $data = $request->validated();
        if (Harbor::query()->where('id', $harbor->id)->where('code', $data['code'])->exists()) {
            throw new InvalidRequestException('港口已存在，请重试！');
        }
        $harbor->update($data);
        return new HarborInfoResource($harbor);
    }

    /**
     * 删除
     * @param Harbor $harbor
     * @return Response
     */
    public function destroy(Harbor $harbor): Response
    {
        $harbor->delete();
        return response()->noContent();
    }
}
