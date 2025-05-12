<?php
/**
 * 集装箱类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContainerType\ContainerTypeResource;
use App\Models\ContainerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContainerTypesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $isPaginate = $request->input('is_paginate');
        if ($isPaginate) {
            $containerTypes = ContainerType::query()->orderByDesc('sort')->paginate();
        } else {
            $containerTypes = ContainerType::query()->orderByDesc('sort')->get();
            ContainerTypeResource::wrap('data');
        }
        return ContainerTypeResource::collection($containerTypes);
    }
}
