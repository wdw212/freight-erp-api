<?php
/**
 * 活动日志 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityLogsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $activityLogs = $adminUser->actions()->orderByDesc('created_at')->paginate();
        return ActivityLogResource::collection($activityLogs);
    }
}
