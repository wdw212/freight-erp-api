<?php
/**
 * 微信
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\OfficialAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->with(function ($message, \Closure $next) use ($app) {
                // 你的自定义逻辑1
                Log::info('打印message');
                Log::info(json_encode($message, JSON_UNESCAPED_UNICODE));

                $msgType = $message['MsgType'];
                switch ($msgType) {
                    case 'image':
                        // 1. 通过 EasyWeChat 获取微信图片流
                        $mediaId = $message['MediaId'];
                        $api = $app->getClient();
                        $response = $api->get('/cgi-bin/media/get', [
                            'media_id' => $mediaId,
                        ]);
                        $imageContent = $response->getContent(); // 读取流内容
                        // 2. 生成七牛云存储的文件名（确保唯一，避免覆盖）
                        $extension = 'jpg';
                        $filename = time() . '_' . Str::random(10) . '.' . $extension;
                        // 路径说明：wechat/images/20251023/xxx.jpg（按日期分类，便于管理）
                        // 3. 上传到七牛云（使用 qiniu 磁盘）
                        $result = Storage::put($filename, $imageContent);
                        if ($result) {
                            // 上传成功：获取七牛上的文件URL
                            $fileUrl = Storage::url($filename);
                            // 记录日志（可选：保存URL到数据库）
                            Log::info('图片上传成功:' . $fileUrl);
                            Cache::put('IMAGE', $fileUrl, 60);
                        }
                        break;
                    case 'text':
                        $content = $message['Content'];
                        if (Str::contains('箱号', $content)) {
                            Log::info('指令解析成功');
                            $content = explode('+', $content);
                            $container = Container::query()->where('no', $content[1])->first();
                            $image = Cache::get('IMAGE');
                            $container->no_image = $image;
                            $container->save();
                            Log::info('上传成功');
                        }
                        break;

                }

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
