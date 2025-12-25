<?php
/**
 * 港口 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Exports\HarborImportTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HarborRequest;
use App\Http\Resources\Api\Harbor\HarborInfoResource;
use App\Http\Resources\Api\Harbor\HarborResource;
use App\Imports\HarborImport;
use App\Models\Harbor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use function Pest\Laravel\get;

class HarborsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->get('keyword', '');
        $isPaginate = $request->get('is_paginate', 1);
        $builder = Harbor::query()->latest();
        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->whereLike('name', '%' . $keyword . '%')
                    ->orWhereLike('code', '%' . $keyword . '%')
                    ->orWhereLike('en_name', '%' . $keyword . '%')
                    ->orWhereLike('country', '%' . $keyword . '%')
                    ->orWhereLike('en_country', '%' . $keyword . '%')
                    ->orWhereLike('route', '%' . $keyword . '%')
                    ->orWhereLike('remark', '%' . $keyword . '%');
            });
        }
        if ($isPaginate) {
            $harbors = $builder->paginate();
        } else {
            $harbors = $builder->get();
            HarborResource::wrap('data');
        }

        return HarborResource::collection($harbors);
    }

    /**
     * 新增
     * @param HarborRequest $request
     * @param Harbor $harbor
     * @return HarborInfoResource
     * @throws InvalidRequestException
     */
    public function store(HarborRequest $request, Harbor $harbor): HarborInfoResource
    {
        $data = $request->validated();
        if (Harbor::query()->where('code', $data['code'])->exists()) {
            throw new InvalidRequestException('港口已存在，请重试！');
        }
        $harbor->fill($data);
        $harbor->save();
        return new HarborInfoResource($harbor);
    }

    /**
     * 详情
     * @param Harbor $harbor
     * @return HarborInfoResource
     */
    public function show(Harbor $harbor): HarborInfoResource
    {
        return new HarborInfoResource($harbor);
    }

    /**
     * 编辑
     * @param HarborRequest $request
     * @param Harbor $harbor
     * @return HarborInfoResource
     * @throws InvalidRequestException
     */
    public function update(HarborRequest $request, Harbor $harbor): HarborInfoResource
    {
        $data = $request->validated();
        if (Harbor::query()->where('id', $harbor->id)->where('code', $data['code'])->exists()) {
            throw new InvalidRequestException('港口已存在，请重试！');
        }
        $harbor->update($data);
        return new HarborInfoResource($harbor);
    }

    /**
     * 删除
     * @param Harbor $harbor
     * @return Response
     */
    public function destroy(Harbor $harbor): Response
    {
        $harbor->delete();
        return response()->noContent();
    }

    /**
     * 导入模版
     * @return JsonResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function importTemplate(): JsonResponse
    {
        $fileName = Str::random(12) . time() . '.xlsx';
        Excel::store(new HarborImportTemplate(), $fileName);
        return response()->json([
            'url' => formatUrl($fileName)
        ]);
    }

    /**
     * 批量导入
     * @param Request $request
     * @return JsonResponse
     */
    public function batchImport(Request $request): JsonResponse
    {
        $file = $request->input('file');
        Excel::import(new HarborImport(), getUrlPath($file));
        return response()->json([
            'message' => '导入成功'
        ]);
    }
}
