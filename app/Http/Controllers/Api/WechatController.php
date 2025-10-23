<?php
/**
 * 微信
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfficialAccountService;
use Illuminate\Http\Request;

class WechatController extends Controller
{
    /**
     * 接入微信公众号
     * @return mixed
     */
    public function serve()
    {
        $app = (new OfficialAccountService())->getApp();
        $server = $app->getServer();

        $server->with(function ($message, \Closure $next) {
            return '谢谢关注！';

            // 你的自定义逻辑
            // return $next($message);
        });

        return $server->serve();
    }
}
