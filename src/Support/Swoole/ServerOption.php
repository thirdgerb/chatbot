<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Swoole;

use Commune\Support\Option\AbsOption;

/**
 * 协程进程池的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *

 *
 * ## Server 配置
 *
 * @property-read string $host
 * @property-read string $port
 *
 * ## Swoole 的服务端配置.
 * 
 * @property-read ServerSettingOption $serverSettings
 * 具体配置项请查看: @see https://wiki.swoole.com/#/server/setting
 *
 * ## 从 ServerSettings 衍生出来的配置
 *
 * @property-read int $workerNum          Worker 进程数
 * @property-read bool $ssl
 */
class ServerOption extends AbsOption
{

    public static function stub(): array
    {
        return [
            'host' => '127.0.0.1',
            'port' => '9501',

            'serverSettings' => [
                //'reactor_num' => 2,
                //'worker_num' => 2,
                //'max_request' => 100000,
                //'max_conn' => 10000,
                //'ssl_cert_file' => '',
                //'ssl_key_file' => '',
            ],
        ];
    }

    public function __get_workerNum() : int
    {
        $settings = $this->serverSettings;
        return $settings->worker_num ?? 2;
    }

    public function __get_ssl() : bool
    {
        $settings = $this->serverSettings;
        // 证书不为空.
        return !empty($settings['ssl_cert_file']);
    }

    public static function relations(): array
    {
        return [
            'serverSettings' => ServerSettingOption::class
        ];
    }


}