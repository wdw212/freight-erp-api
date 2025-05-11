<?php
/**
 * 集装箱类型 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContainerType\ContainerTypeResource;
use App\Models\ContainerType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContainerTypesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $containerTypes = ContainerType::query()->orderByDesc('sort')->get();
        ContainerTypeResource::wrap('data');
        return ContainerTypeResource::collection($containerTypes);
    }
}
