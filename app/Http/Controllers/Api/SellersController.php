<?php
/**
 * 销货单位 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRequest;
use App\Http\Resources\Seller\SellerInfoResource;
use App\Http\Resources\Seller\SellerResource;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SellersController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate', 1);
        $builder = Seller::query()->latest();
        if ($isPaginate) {
            $sellers = $builder->paginate();
        } else {
            $sellers = $builder->get();
            SellerResource::wrap('data');
        }
        return SellerResource::collection($sellers);
    }

    /**
     * 新增
     * @param SellerRequest $request
     * @param Seller $seller
     * @return SellerInfoResource
     */
    public function store(SellerRequest $request, Seller $seller): SellerInfoResource
    {
        $seller->fill($request->validated());
        $seller->save();
        return new SellerInfoResource($seller);
    }

    /**
     * 详情
     * @param Seller $seller
     * @return SellerInfoResource
     */
    public function show(Seller $seller): SellerInfoResource
    {
        return new SellerInfoResource($seller);
    }

    /**
     * 编辑
     * @param Request $request
     * @param Seller $seller
     * @return SellerInfoResource
     */
    public function update(Request $request, Seller $seller): SellerInfoResource
    {
        $seller->fill($request->all());
        $seller->save();
        return new SellerInfoResource($seller);
    }

    /**
     * @param Seller $seller
     * @return Response
     */
    public function destroy(Seller $seller): Response
    {
        $seller->delete();
        return response()->noContent();
    }
}
