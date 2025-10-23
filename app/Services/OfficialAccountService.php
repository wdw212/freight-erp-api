<?php

namespace App\Services;

use EasyWeChat\OfficialAccount\Application;

class OfficialAccountService
{
    /**
     * @var mixed
     */
    public mixed $app;

    public function __construct()
    {
        $this->setApp();
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return void
     */
    public function setApp(): void
    {
        $config = [
            'app_id' => 'wx29441c8e6686824f',
            'secret' => '5fa61b1e3fe08abad4721b63348a046b',
            'token' => 'd0fdcf03159570ec63770436d1e156c0',
            'aes_key' => '', // 明文模式请勿填写 EncodingAESKey
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * redirect_url：OAuth授权完成后的回调页地址
             */
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'redirect_url' => '/examples/oauth_callback.php',
            ],

            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
             */
            'http' => [
                'timeout' => 5.0,
                'retry' => true, // 使用默认重试配置
            ],
        ];

        $this->app = new Application($config);
    }
}
