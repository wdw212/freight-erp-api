<?php
/**
 * 备注 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RemarkRequest;
use App\Http\Resources\Remark\RemarkInfoResource;
use App\Http\Resources\Remark\RemarkResource;
use App\Models\Remark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RemarksController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $keyword = $request->input('keyword', '');
        $builder = Remark::query()->whereBelongsTo($adminUser)->latest();
        if (!empty($keyword)) {
            $builder = $builder->whereLike('content', '%' . $keyword . '%');
        }
        $remarks = $builder->get();
        RemarkResource::wrap('data');
        return RemarkResource::collection($remarks);
    }

    /**
     * 新增
     * @param RemarkRequest $request
     * @param Remark $remark
     * @return RemarkInfoResource
     */
    public function store(RemarkRequest $request, Remark $remark): RemarkInfoResource
    {
        $adminUser = $request->user();
        $remark->fill($request->all());
        $remark->adminUser()->associate($adminUser);
        $remark->save();
        return new RemarkInfoResource($remark);
    }

    /**
     * 详情
     * @param Remark $remark
     * @return RemarkInfoResource
     */
    public function show(Remark $remark): RemarkInfoResource
    {
        return new RemarkInfoResource($remark);
    }

    /**
     * 删除
     * @param Remark $remark
     * @return Response
     */
    public function destroy(Remark $remark): Response
    {
        $remark->delete();
        return response()->noContent();
    }

    /**
     * 编辑
     * @param RemarkRequest $request
     * @param Remark $remark
     * @return RemarkInfoResource
     */
    public function update(RemarkRequest $request, Remark $remark): RemarkInfoResource
    {
        $remark->fill($request->all());
        $remark->update();
        return new RemarkInfoResource($remark);
    }

    /**
     * 批量新增或创建
     * @param Request $request
     * @param Remark $remark
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function batchStoreOrUpdate(Request $request, Remark $remark): JsonResponse
    {
        $adminUser = $request->user();
        $items = $request->input('items');
        if (empty($items)) {
            throw new InvalidRequestException('缺少必要参数,请重试！');
        }
        $items = json_decode($items, true);
        foreach ($items as $item) {
            if (isset($item['id'])) {
                Remark::query()->where('id', $item['id'])->update(['content' => $item['content']]);
            } else {
                Remark::query()->whereBelongsTo($adminUser)->create($item);
            }
        }
        return response()->json([
            'message' => '操作成功!'
        ]);
    }
}
