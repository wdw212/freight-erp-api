<?php
/**
 * 微信
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfficialAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    /**
     * 接入微信公众号
     * @return mixed
     */
    public function server()
    {
        $app = (new OfficialAccountService())->getApp();
        $server = $app->getServer();

        $server->addEventListener('subscribe', function ($message, \Closure $next) {
            return '感谢您关注!';
        });

        $server
            ->with(function ($message, \Closure $next) {
                // 你的自定义逻辑1
                Log::info('打印message');
                Log::info(json_encode($message, JSON_UNESCAPED_UNICODE));
                return $next($message);
            })
            ->with(function ($message, \Closure $next) {
                // 你的自定义逻辑2
                return $next($message);
            })
            ->with(function ($message, \Closure $next) {
                // 你的自定义逻辑3
                return $next($message);
            });


        return $server->serve();
    }
}
