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
 * Swoole Server 的基础配置.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ServerSettingOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'enable_coroutine' => true,
            'worker_num' => swoole_cpu_num(),
            //'pid_file' => BASE_PATH . '/runtime/hyperf.pid',
            'open_tcp_nodelay' => true,
            'max_coroutine' => 100000,
            'open_http2_protocol' => true,
            'max_request' => 100000,
            'socket_buffer_size' => 2 * 1024 * 1024,
            'buffer_output_size' => 2 * 1024 * 1024,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}