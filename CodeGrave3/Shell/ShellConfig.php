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

use Commune\Shell\Prototype\IShellKernel;
use Commune\Shell\Prototype\Kernels\IRequestKernel;
use Commune\Shell\Providers\ShellSessionServiceProvider;
use Commune\Support\Struct\AbsStruct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shellName                 Shell的名称
 *
 * @property-read string[] $pipeline                Shell运行的管道
 *
 * @property-read string[] $scenes                  Shell允许的场景
 *
 * @property-read string $kernel
 *
 * @property-read string[] $providers
 *
 * @property-read int $sessionExpire                shell session 的过期时间.
 *
 * @property-read bool $isDuplex                    shell 的消息是否是双通的.
 */
class ShellConfig extends AbsStruct
{

    const IDENTITY = 'shellName';

    public static function stub(): array
    {
        return [
            'shellName' => 'test',

            'sessionExpire' => 3600,

            'scenes' => [
            ],

            'providers' => [
                ShellSessionServiceProvider::class,
            ],

            'kernel' => IShellKernel::class,

            'pipeline' => [

            ],

        ];
    }


    public function allowScene(string $id) : bool
    {
        return in_array($id, $this->scenes);
    }
}