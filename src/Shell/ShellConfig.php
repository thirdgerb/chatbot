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

use Commune\Message\Blueprint\QuestionMsg;
use Commune\Shell\Prototype\Kernels\IRequestKernel;
use Commune\Shell\Prototype\Pipeline\SendToGhostPipe;
use Commune\Shell\Prototype\Pipeline\RenderPipe;
use Commune\Shell\Prototype\Pipeline\ResponsePipe;
use Commune\Shell\Providers\ShlSessionServiceProvider;
use Commune\Support\Struct\Structure;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 *
 * @property-read string $shellName                 Shell的名称
 *
 * @property-read string[] $pipeline                Shell运行的管道
 * @property-read string[] $directives              Shell 预加载的命令. id => DirectiveClass
 *
 * @property-read string $requestKernel
 *
 * @property-read string[] $providers
 *
 * @property-read int $sessionExpire                shell session 的过期时间.
 *
 * @property-read bool $isBroadcasting              shell 的消息发布是通过广播方式.
 */
class ShellConfig extends Structure
{

    const IDENTITY = 'shellName';

    public static function stub(): array
    {
        return [
            'shellName' => 'test',

            'pipeline' => [
                // 发送响应
                ResponsePipe::class,
                // 检查问题, 尝试回答
                QuestionMsg::class,
                RenderPipe::class,
                SendToGhostPipe::class,
            ],

            'sessionExpire' => 3600,

            'providers' => [
                ShlSessionServiceProvider::class,
            ],

            'requestKernel' => IRequestKernel::class,

        ];
    }
}