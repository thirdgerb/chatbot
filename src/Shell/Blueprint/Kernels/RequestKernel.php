<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Kernels;

use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;

/**
 * 处理单个请求的 Kernel, 同步的请求给予同步的响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RequestKernel
{
    /**
     * 完成同步响应.
     *
     * @param ShlRequest $request
     * @param ShlResponse $response
     */
    public function onRequest(
        ShlRequest $request,
        ShlResponse $response
    ) : void;

}