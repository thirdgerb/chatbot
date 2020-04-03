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

use Commune\Shell\Prototype\Kernels\IRequestKernel;
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
 * @property-read string $kernel
 *
 * @property-read string[] $providers
 *
 * @property-read int $sessionExpire                shell session 的过期时间.
 *
 * @property-read bool $isDuplex                    shell 的消息是否是双通的.
 */
class ShellConfig extends Structure
{

    const IDENTITY = 'shellName';

    public static function stub(): array
    {
        return [
            'shellName' => 'test',

            'sessionExpire' => 3600,

            'providers' => [
                ShlSessionServiceProvider::class,
            ],

            'kernel' => IRequestKernel::class,

            'pipeline' => [

            ],

        ];
    }
}