<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Options;

use Commune\Ghost\Prototype\Kernels\IApiKernel;
use Commune\Ghost\Prototype\Kernels\IMessageKernel;
use Commune\Support\Struct\AbsStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class KernelOption extends AbsStruct
{
    public static function stub(): array
    {
        return [

            // api kernel
            'apiKernel' => IApiKernel::class,
            'apiPipeline' => [
            ],

            // message kernel
            'messageKernel' => IMessageKernel::class,
            'messagePipeline' => [
            ],

            // async kernel
        ];
    }


}