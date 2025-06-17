<?php
/**
 * 装箱地址 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoadingAddressRequest;
use App\Http\Resources\LoadingAddress\LoadingAddressInfoResource;
use App\Http\Resources\LoadingAddress\LoadingAddressResource;
use App\Models\LoadingAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LoadingAddressesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate', 1);
        $keyword = $request->input('keyword', '');

        $builder = LoadingAddress::query()->with(['region:id,name', 'adminUser:id,name'])->latest();

        if (!empty($keyword)) {
            $builder = $builder->whereLike('keyword', '%' . $keyword . '%')
                ->orWhereLike('address', '%' . $keyword . '%')
                ->orWhereLike('contact_name', '%' . $keyword . '%')
                ->orWhereLike('phone', '%' . $keyword . '%');
        }

        if ($isPaginate) {
            $loadingAddresses = $builder->paginate();
        } else {
            $loadingAddresses = $builder->get();
            LoadingAddressResource::wrap('data');
        }
        return LoadingAddressResource::collection($loadingAddresses);
    }

    /**
     * 新增
     * @param LoadingAddressRequest $request
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     */
    public function store(LoadingAddressRequest $request, LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        $adminUser = $request->user();
        $loadingAddress->fill($request->all());
        $loadingAddress->adminUser()->associate($adminUser);
        $loadingAddress->save();
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 详情
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     */
    public function show(LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 编辑
     * @param LoadingAddressRequest $request
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     */
    public function update(LoadingAddressRequest $request, LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        $loadingAddress->fill($request->all());
        $loadingAddress->update();
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 删除
     * @param LoadingAddress $loadingAddress
     * @return Response
     */
    public function destroy(LoadingAddress $loadingAddress): Response
    {
        $loadingAddress->delete();
        return response()->noContent();
    }
}
