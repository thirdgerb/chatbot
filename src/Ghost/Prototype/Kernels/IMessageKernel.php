<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Kernels;

use Commune\Ghost\Blueprint\Kernels\MessageKernel;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMessageKernel implements MessageKernel
{
    public function onRequest(
        GhtRequest $request,
        GhtResponse $response
    ): void
    {
    }


}