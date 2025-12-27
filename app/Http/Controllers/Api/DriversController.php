<?php
/**
 * 司机 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DriverRequest;
use App\Http\Resources\Api\Driver\DriverInfoResource;
use App\Http\Resources\Api\Driver\DriverResource;
use App\Models\Driver;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DriversController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword', '');
        $isPaginate = $request->input('is_paginate', 1);

        $builder = Driver::query()->latest();

        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->whereLike('name', "%{$keyword}%")
                    ->orWhereLike('phone', "%{$keyword}%")
                    ->orWhereLike('remark', "%{$keyword}%");
            });
        }

        if ($isPaginate) {
            $drivers = $builder->paginate();
        } else {
            $drivers = $builder->get();
            DriverResource::wrap('data');
        }

        return DriverResource::collection($drivers);
    }

    /**
     * 新增
     * @param DriverRequest $request
     * @param Driver $driver
     * @return DriverInfoResource
     */
    public function store(DriverRequest $request, Driver $driver): DriverInfoResource
    {
        $driver->fill($request->all());
        $driver->save();
        return new DriverInfoResource($driver);
    }

    /**
     * 详情
     * @param Driver $driver
     * @return DriverInfoResource
     */
    public function show(Driver $driver): DriverInfoResource
    {
        return new DriverInfoResource($driver);
    }

    /**
     * 编辑
     * @param DriverRequest $request
     * @param Driver $driver
     * @return DriverInfoResource
     */
    public function update(DriverRequest $request, Driver $driver): DriverInfoResource
    {
        $driver->fill($request->all());
        $driver->update();
        return new DriverInfoResource($driver);
    }

    /**
     * 删除
     * @param Driver $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        $driver->delete();
        return response()->noContent();
    }
}
