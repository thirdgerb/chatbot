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

use Commune\Blueprint\Shell\Parser\InputParser;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Kernel;
use Commune\Shell\IShellConfig;
use Commune\Protocals\HostMsg;
use Commune\Blueprint\Kernel\Protocals;
use Commune\Blueprint\Kernel\Handlers;
use Commune\Shell\Providers\ShellSessionServiceProvider;
use Commune\Shell\Render\SystemIntentRenderer;
use Commune\Shell\Render\TranslatorRenderer;
use Commune\Support\Protocal\ProtocalOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellProtoConfig extends IShellConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'providers' => [
                // shell 请求级服务.
                ShellSessionServiceProvider::class,
            ],
            'options' => [],
            'components' => [],

            /**
             * App 可以处理的各种协议.
             *
             * @see ProtocalOption
             * key 可以自己定义, 方便子类修改. 也可以不定义.
             */
            'protocals' => [

                /**
                 * App Request Handler
                 * App 负责处理请求的内核.
                 */

                [
                    'protocal' => Protocals\ShellInputRequest::class,
                    'interface' => Handlers\ShellInputReqHandler::class,
                    // 默认的 handler
                    'default' => Kernel\Handlers\IShellInputReqHandler::class,
                ],
                [
                    'protocal' => Protocals\ShellOutputRequest::class,
                    'interface' => Handlers\ShellOutputReqHandler::class,
                    // 默认的 handler
                    'default' => Kernel\Handlers\IShellOutputReqHandler::class,
                ],

                /**
                 * Api Parser
                 * 负责把输入消息进行转义.
                 */
                [
                    'interface' => InputParser::class,
                    'protocal' => HostMsg::class,
                ],

                /**
                 * Renderer
                 */
                // 系统命令的 handler
                [
                    'protocal' => HostMsg\IntentMsg::class,
                    'interface' => Renderer::class,
                    'handlers' => [
                        [
                            'handler' => SystemIntentRenderer::class,
                            'filters' => [
                                'system.*'
                            ],
                            'params' => [],
                        ],
                    ],
                    'default' => TranslatorRenderer::class,
                ],

                // 默认 handler
                [
                    'protocal' => HostMsg::class,
                    'interface' => Renderer::class,
                    'default' => TranslatorRenderer::class,
                ],

            ],
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 0,
        ];
    }

}