<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Prototype;

use Commune\Kernel;
use Commune\Shell\IShellConfig;
use Commune\Blueprint\Kernel\Protocals;
use Commune\Blueprint\Kernel\Handlers;
use Commune\Shell\Providers\ShellSessionServiceProvider;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellProtoConfig extends IShellConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'name' => 'demo',
            'providers' => [
                // shell 请求级服务.
                ShellSessionServiceProvider::class,
            ],
            'options' => [],
            'components' => [],
            'protocals' => [
                [
                    'protocal' => Protocals\ShellInputRequest::class,
                    'interface' => Handlers\ShellRequestHandler::class,
                    // 默认的 handler
                    'default' => Kernel\Handlers\IShellRequestHandler::class,
                ],
                [
                    'protocal' => Protocals\ShellOutputRequest::class,
                    'interface' => Handlers\ShellOutputHandler::class,
                    // 默认的 handler
                    'default' => Kernel\Handlers\IShellOutputHandler::class,
                ],
            ],
            'sessionExpire' => 864000,
            'sessionLockerExpire' => 0,
        ];
    }

}