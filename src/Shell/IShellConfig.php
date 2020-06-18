<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Kernel\Handlers\ShellOutputHandler;
use Commune\Blueprint\Kernel\Handlers\ShellRequestHandler;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Kernel\Handlers\IShellRequestHandler;
use Commune\Support\Option\AbsOption;
use Commune\Support\Protocal\ProtocalOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellConfig extends AbsOption implements ShellConfig
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'providers' => [],
            'options' => [],
            'components' => [],
            'protocals' => [
                [
                    'protocal' => ShellInputRequest::class,
                    'interface' => ShellRequestHandler::class,
                    'handlers' => [
                        'handler' => IShellRequestHandler::class,
                    ],
                ],
                [
                    'protocal' => ShellOutputRequest::class,
                    'interface' => ShellOutputHandler::class,
                    'handlers' => [

                    ],
                ]

            ],
            'sessionExpire' => 864000,
            'sessionLockerExpire' => 0,
        ];
    }

    public static function relations(): array
    {
        return [
            'protocals[]' => ProtocalOption::class,
        ];
    }


}