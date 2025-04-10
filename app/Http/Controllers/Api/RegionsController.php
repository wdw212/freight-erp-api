<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;
use App\Http\Resources\Region\RegionInfoResource;
use App\Http\Resources\Region\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RegionsController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $regions = Region::query()->where('parent_id', 0)->with('children')->get();
        RegionResource::wrap('data');
        return RegionResource::collection($regions);
    }

    /**
     * 新增
     * @param RegionRequest $request
     * @param Region $region
     * @return RegionInfoResource
     */
    public function store(RegionRequest $request, Region $region): RegionInfoResource
    {
        $region->fill($request->all());
        $region->save();
        return new RegionInfoResource($region);
    }

    /**
     * 详情
     * @param Region $region
     * @return RegionInfoResource
     */
    public function show(Region $region): RegionInfoResource
    {
        return new RegionInfoResource($region);
    }

    /**
     * 编辑
     * @param RegionRequest $request
     * @param Region $region
     * @return RegionInfoResource
     */
    public function update(RegionRequest $request, Region $region): RegionInfoResource
    {
        $region->fill($request->all());
        $region->update();
        return new RegionInfoResource($region);
    }

    /**
     * 删除
     * @param Region $region
     * @return Response
     */
    public function destroy(Region $region): Response
    {
        $region->delete();
        return response()->noContent();
    }
}
