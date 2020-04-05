<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint;

use Commune\Framework\Blueprint\AppKernel;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellKernel extends AppKernel
{

    /**
     * 同步请求
     * @param ShlRequest $request
     * @param ShlResponse $response
     */
    public function onSync(
        ShlRequest $request,
        ShlResponse $response
    ): void;

    /**
     * 异步接受响应.
     *
     * @param ShlResponse $response
     */
    public function onAsyncResponse(
        ShlResponse $response
    ) : void;


    /**
     * 异步发送请求.
     * @param ShlRequest $request
     */
    public function onAsyncRequest(
        ShlRequest $request
    ) : void;
}