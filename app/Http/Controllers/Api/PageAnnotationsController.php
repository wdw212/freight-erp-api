<?php
/**
 * 页面注明 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageAnnotationRequest;
use App\Http\Resources\PageAnnotation\PageAnnotationInfoResource;
use App\Http\Resources\PageAnnotation\PageAnnotationResource;
use App\Models\CompanyHeader;
use App\Models\LoadingAddress;
use App\Models\PageAnnotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PageAnnotationsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $pageAnnotations = PageAnnotation::query()->paginate();
        return PageAnnotationResource::collection($pageAnnotations);
    }

    /**
     * 新增
     * @param PageAnnotationRequest $request
     * @param PageAnnotation $pageAnnotation
     * @return PageAnnotationInfoResource
     * @throws InvalidRequestException
     */
    public function store(PageAnnotationRequest $request, PageAnnotation $pageAnnotation): PageAnnotationInfoResource
    {
        $modelType = $request->input('model_type');
        $modelType = PageAnnotation::$getModelType[$modelType];
        $oldPageAnnotation = PageAnnotation::query()->where('model_type', $modelType)->first();
        if ($oldPageAnnotation) {
            throw new InvalidRequestException('页面注明已存在，请去修改！');
        }
        $pageAnnotation->model_type = $modelType;
        $pageAnnotation->content = $request->input('content');
        $pageAnnotation->save();
        return new PageAnnotationInfoResource($pageAnnotation);
    }

    /**
     * 详情
     * @param PageAnnotation $pageAnnotation
     * @return PageAnnotationInfoResource
     */
    public function show(PageAnnotation $pageAnnotation): PageAnnotationInfoResource
    {
        return new PageAnnotationInfoResource($pageAnnotation);
    }

    /**
     * 编辑
     * @param PageAnnotationRequest $request
     * @param PageAnnotation $pageAnnotation
     * @return PageAnnotationInfoResource
     * @throws InvalidRequestException
     */
    public function update(PageAnnotationRequest $request, PageAnnotation $pageAnnotation): PageAnnotationInfoResource
    {
        $modelType = $request->input('model_type');

        $oldPageAnnotation = PageAnnotation::query()
            ->where('model_type', $modelType)
            ->whereNot('id', $pageAnnotation->id)
            ->first();
        if ($oldPageAnnotation) {
            throw new InvalidRequestException('页面注明已存在，请去修改！');
        }
        $modelType = PageAnnotation::$getModelType[$modelType];
        $pageAnnotation->model_type = $modelType;
        $pageAnnotation->content = $request->input('content');
        $pageAnnotation->save();
        return new PageAnnotationInfoResource($pageAnnotation);
    }

    /**
     * 删除
     * @param PageAnnotation $pageAnnotation
     * @return Response
     */
    public function destroy(PageAnnotation $pageAnnotation): Response
    {
        $pageAnnotation->delete();
        return response()->noContent();
    }

    /**
     * 获取模型类型
     * @param Request $request
     * @return JsonResponse
     */
    public function getModelTypes(Request $request): JsonResponse
    {
        $data = [
            'loading_address' => '装柜地址',
            'company_header' => '公司抬头',
            'sft_record' => '收发通',
            'region' => '地区',
        ];
        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * 根据模型类型获取详情
     * @param Request $request
     * @return PageAnnotationInfoResource
     */
    public function getShowByModelType(Request $request): PageAnnotationInfoResource
    {
        $modelType = $request->input('model_type');
        $modelType = PageAnnotation::$getModelType[$modelType];
        $pageAnnotation = PageAnnotation::query()->where('model_type', $modelType)->first();
        return new PageAnnotationInfoResource($pageAnnotation);
    }
}
