<?php
/**
 * 微信
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfficialAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                        Log::info('--打印请求媒体的结果--');
                        Log::info($response);
//                        $imageContent = $media->getBody()->getContents(); // 读取流内容
//
//                        // 2. 生成七牛云存储的文件名（确保唯一，避免覆盖）
//                        $mimeType = $media->getHeaderLine('Content-Type');
//                        $mimeTypeMap = [
//                            'image/jpeg' => 'jpg',
//                            'image/png' => 'png',
//                            'image/gif' => 'gif',
//                        ];
//                        $extension = $mimeTypeMap[$mimeType] ?? 'jpg';
//                        $fileName = date('Ymd') . '/' . time() . '.' . $extension;
//                        // 路径说明：wechat/images/20251023/xxx.jpg（按日期分类，便于管理）
//                        // 3. 上传到七牛云（使用 qiniu 磁盘）
//                        $result = Storage::put($fileName, $imageContent);
//                        if ($result) {
//                            // 上传成功：获取七牛上的文件URL
//                            $fileUrl = Storage::url($fileName);
//                            // 记录日志（可选：保存URL到数据库）
//                            Log::info('图片上传成功:' . $fileUrl);
//                            return $fileUrl; // 返回URL供后续使用
//                        }

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
