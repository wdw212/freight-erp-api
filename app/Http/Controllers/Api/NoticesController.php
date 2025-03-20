<?php
/**
 * 公告 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoticeRequest;
use App\Http\Resources\Notice\NoticeInfoResource;
use App\Http\Resources\Notice\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoticesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $notices = Notice::query()->with('adminUser:id,name')->orderByDesc('created_at')->paginate();
        return NoticeResource::collection($notices);
    }

    /**
     * 创建
     * @param NoticeRequest $request
     * @param Notice $notice
     * @return NoticeInfoResource
     */
    public function store(NoticeRequest $request, Notice $notice): NoticeInfoResource
    {
        $adminUser = $request->user();
        $notice->fill($request->all());
        $notice->adminUser()->associate($adminUser);
        $notice->save();
        return new NoticeInfoResource($notice);
    }

    /**
     * 详情
     * @param Notice $notice
     * @return NoticeInfoResource
     */
    public function show(Notice $notice): NoticeInfoResource
    {
        return new NoticeInfoResource($notice);
    }

    /**
     * 编辑
     * @param NoticeRequest $request
     * @param Notice $notice
     * @return NoticeInfoResource
     */
    public function update(NoticeRequest $request, Notice $notice): NoticeInfoResource
    {
        $notice->fill($request->all());
        $notice->update();
        return new NoticeInfoResource($notice);
    }

    /**
     * 删除
     * @param Notice $notice
     * @return Response
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return response()->noContent();
    }
}
