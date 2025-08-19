<?php
/**
 * 码头 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WharfRequest;
use App\Http\Resources\Wharf\WharfInfoResource;
use App\Http\Resources\Wharf\WharfResource;
use App\Models\Wharf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WharvesController extends Controller
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
        $builder = Wharf::query()->orderByDesc('sort');

        if (!empty($keyword)) {
            $builder = $builder->where('name', 'like', '%' . $keyword . '%');
        }

        if ($isPaginate) {
            $wharves = $builder->paginate();
        } else {
            $wharves = $builder->get();
            WharfResource::wrap('data');
        }
        return WharfResource::collection($wharves);
    }

    /**
     * 新增
     * @param WharfRequest $request
     * @param Wharf $wharf
     * @return WharfInfoResource
     */
    public function store(WharfRequest $request, Wharf $wharf): WharfInfoResource
    {
        $wharf->fill($request->all());
        $wharf->save();
        return new WharfInfoResource($wharf);
    }

    /**
     * 详情
     * @param Wharf $wharf
     * @return WharfInfoResource
     */
    public function show(Wharf $wharf): WharfInfoResource
    {
        return new WharfInfoResource($wharf);
    }

    /**
     * 编辑
     * @param WharfRequest $request
     * @param Wharf $wharf
     * @return WharfInfoResource
     */
    public function update(WharfRequest $request, Wharf $wharf): WharfInfoResource
    {
        $wharf->fill($request->all());
        $wharf->update();
        return new WharfInfoResource($wharf);
    }

    /**
     * 删除
     * @param Wharf $wharf
     * @return Response
     */
    public function destroy(Wharf $wharf): Response
    {
        $wharf->delete();
        return response()->noContent();
    }
}
