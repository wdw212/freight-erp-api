<?php
/**
 * 车队
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FleetRequest;
use App\Http\Resources\Fleet\FleetInfoResource;
use App\Http\Resources\Fleet\FleetResource;
use App\Models\Fleet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FleetsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword');
        $isPaginate = $request->input('is_paginate');

        $builder = Fleet::query();

        if (!empty($keyword)) {
            $builder = $builder->where('name', 'like', '%' . $keyword . '%');
        }

        if ($isPaginate) {
            $fleets = $builder->paginate();
        } else {
            $fleets = $builder->get();
            FleetResource::wrap('data');
        }

        return FleetResource::collection($fleets);
    }

    /**
     * 新增
     * @param FleetRequest $request
     * @param Fleet $fleet
     * @return FleetInfoResource
     * @throws InvalidRequestException
     */
    public function store(FleetRequest $request, Fleet $fleet): FleetInfoResource
    {
        $data = $request->all();

        // 检查名称是否存在
        if (Fleet::query()->where('name', $data['name'])->exists()) {
            throw new InvalidRequestException($data['name'] . ' 已经存在，请重试！');
        }

        $fleet->fill($request->all());
        $fleet->save();
        return new FleetInfoResource($fleet);
    }

    /**
     * 详情
     * @param Fleet $fleet
     * @return FleetInfoResource
     */
    public function show(Fleet $fleet): FleetInfoResource
    {
        return new FleetInfoResource($fleet);
    }

    /**
     * 编辑
     * @param FleetRequest $request
     * @param Fleet $fleet
     * @return FleetInfoResource
     * @throws InvalidRequestException
     */
    public function update(FleetRequest $request, Fleet $fleet): FleetInfoResource
    {
        $data = $request->all();

        // 检查名称是否存在
        if (Fleet::query()->whereNot('id', $fleet->id)->where('name', $data['name'])->exists()) {
            throw new InvalidRequestException($data['name'] . ' 已经存在，请重试！');
        }

        $fleet->fill($request->all());
        $fleet->update();
        return new FleetInfoResource($fleet);
    }

    /**
     * 删除
     * @param Fleet $fleet
     * @return Response
     */
    public function destroy(Fleet $fleet): Response
    {
        $fleet->delete();
        return response()->noContent();
    }
}
