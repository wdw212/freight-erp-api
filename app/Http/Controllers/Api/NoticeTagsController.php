<?php
/**
 * 公告标签
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoticeTagRequest;
use App\Http\Resources\NoticeTag\NoticeTagInfoResource;
use App\Http\Resources\NoticeTag\NoticeTagResource;
use App\Models\NoticeTag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoticeTagsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $noticeTags = NoticeTag::query()->get();
        NoticeTagResource::wrap('data');
        return NoticeTagResource::collection($noticeTags);
    }

    /**
     * 新增
     * @param NoticeTagRequest $request
     * @param NoticeTag $noticeTag
     * @return NoticeTagInfoResource
     */
    public function store(NoticeTagRequest $request, NoticeTag $noticeTag): NoticeTagInfoResource
    {
        $noticeTag->fill($request->all());
        $noticeTag->save();
        return new NoticeTagInfoResource($noticeTag);
    }

    /**
     * 详情
     * @param NoticeTag $noticeTag
     * @return NoticeTagInfoResource
     */
    public function show(NoticeTag $noticeTag): NoticeTagInfoResource
    {
        return new NoticeTagInfoResource($noticeTag);
    }

    /**
     * 编辑
     * @param NoticeTagRequest $request
     * @param NoticeTag $noticeTag
     * @return NoticeTagInfoResource
     */
    public function update(NoticeTagRequest $request, NoticeTag $noticeTag): NoticeTagInfoResource
    {
        $noticeTag->fill($request->all());
        $noticeTag->update();
        return new NoticeTagInfoResource($noticeTag);
    }

    /**
     * 删除
     * @param NoticeTag $noticeTag
     * @return Response
     */
    public function destroy(NoticeTag $noticeTag): Response
    {
        $noticeTag->delete();
        return response()->noContent();
    }
}
