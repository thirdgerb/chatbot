<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Kernels;

use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;

/**
 * API 调用的运行内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiKernel
{

    public function onRequest(
        GhtRequest $request,
        GhtResponse $response
    ) : void;


}