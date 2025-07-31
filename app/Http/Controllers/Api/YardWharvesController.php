<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\YardWharfRequest;
use App\Http\Resources\YardWharf\YardWharfInfoResource;
use App\Http\Resources\YardWharf\YardWharfResource;
use App\Models\YardWharf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class YardWharvesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword', '');
        $type = $request->input('type', '');
        $isPaginate = $request->input('is_paginate', 1);

        $builder = YardWharf::query()->orderByDesc('id');

        if (!empty($keyword)) {
            $builder = $builder->where('name', 'like', "%{$keyword}%");
        }
        if (!empty($type)) {
            $builder = $builder->where('type', $type);
        }
        if ($isPaginate) {
            $yardWharves = $builder->paginate();
        } else {
            $yardWharves = $builder->get();
            YardWharfResource::wrap('data');
        }
        return YardWharfResource::collection($yardWharves);
    }

    /**
     * 新增
     * @param YardWharfRequest $request
     * @param YardWharf $yardWharf
     * @return YardWharfInfoResource
     */
    public function store(YardWharfRequest $request, YardWharf $yardWharf): YardWharfInfoResource
    {
        $yardWharf->fill($request->all());
        $yardWharf->save();
        return new YardWharfInfoResource($yardWharf);
    }

    /**
     * 详情
     * @param YardWharf $yardWharf
     * @return YardWharfInfoResource
     */
    public function show(YardWharf $yardWharf): YardWharfInfoResource
    {
        return new YardWharfInfoResource($yardWharf);
    }

    /**
     * 编辑
     * @param YardWharfRequest $request
     * @param YardWharf $yardWharf
     * @return YardWharfInfoResource
     */
    public function update(YardWharfRequest $request, YardWharf $yardWharf): YardWharfInfoResource
    {
        $yardWharf->fill($request->all());
        $yardWharf->update();
        return new YardWharfInfoResource($yardWharf);
    }

    /**
     * 删除
     * @param YardWharf $yardWharf
     * @return Response
     */
    public function destroy(YardWharf $yardWharf)
    {
        $yardWharf->delete();
        return response()->noContent();
    }
}
