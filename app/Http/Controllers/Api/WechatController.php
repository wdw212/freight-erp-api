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
        return $app->server->serve();
    }
}
