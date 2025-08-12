<?php
/**
 * 消息通知 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NotificationsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $notifications = $adminUser->notifications()->latest()->paginate();
        return NotificationResource::collection($notifications);
    }

    /**
     * 未读列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function unreadIndex(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $notifications = $adminUser->unreadNotifications()->orderByDesc('created_at')->get();
        return NotificationResource::collection($notifications);
    }

    /**
     * 标记已读
     * @param Request $request
     * @return Response
     */
    public function markAsRead(Request $request): Response
    {
        $adminUser = $request->user();
        $adminUser->unreadNotifications()->update(['read_at' => now()]);
        return response()->noContent();
    }

    /**
     * 消息已读
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function read(Request $request, $id): Response
    {
        $adminUser = $request->user();
        $adminUser->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->noContent();
    }

    /**
     * 消息删除
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function destroy(Request $request, $id): Response
    {
        $adminUser = $request->user();
        $adminUser->notifications()->findOrFail($id)?->delete();
        return response()->noContent();
    }

    /**
     * 清空
     * @param Request $request
     * @return Response
     */
    public function clear(Request $request): Response
    {
        $adminUser = $request->user();
        $adminUser->notifications()->delete();
        return response()->noContent();
    }
}
