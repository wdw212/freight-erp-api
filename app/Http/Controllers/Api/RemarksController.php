<?php
/**
 * 备注 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemarkRequest;
use App\Http\Resources\Remark\RemarkInfoResource;
use App\Http\Resources\Remark\RemarkResource;
use App\Models\Remark;
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
        $remarks = Remark::query()->latest()->get();
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
     * 删除
     * @param Remark $remark
     * @return Response
     */
    public function destroy(Remark $remark): Response
    {
        $remark->delete();
        return response()->noContent();
    }
}
