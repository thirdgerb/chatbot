<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Ghost\Prototype\Kernels\IApiKernel;
use Commune\Ghost\Prototype\Kernels\IAsyncKernel;
use Commune\Ghost\Prototype\Kernels\IMessageKernel;
use Commune\Support\Struct\Structure;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $messageKernel
 * @property-read string $apiKernel
 * @property-read string $asyncKernel
 *
 * @property-read array $providers 需要注册的服务
 *  [
 *      'providerClass',  # 直接用类名来注册
 *      'providerClass' => [ configs ]  # 使用类名, 同时设定初始值
 *  ]
 *
 * @property-read string $defaultComprehension
 */
class GhostConfig extends Structure
{
    public static function stub(): array
    {
        return [

            // kernel
            'messageKernel' => IMessageKernel::class,
            'apiKernel' => IApiKernel::class,
            'asyncKernel' => IAsyncKernel::class,

            'providers' => [

            ],

        ];
    }


}