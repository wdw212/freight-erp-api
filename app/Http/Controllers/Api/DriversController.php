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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DriversController extends Controller
{
    /**
     * 列表
     * @bodyParam keyword string required 搜索关键词 Example: 手机壳
     * @response 200 {"msg": "记录成功"}
     * @response 422 {"message": "The keyword field is required."}
     * @return AnonymousResourceCollection
     */
    #[BodyParam(name: 'keyword', type: 'string', description: '搜索关键词', required: true, example: '手机壳')]
    public function index(): AnonymousResourceCollection
    {
        $drivers = Driver::query()->latest()->paginate();
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
