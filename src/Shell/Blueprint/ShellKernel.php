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
use Commune\Shell\Contracts\ShellRequest;
use Commune\Shell\Contracts\ShellResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellKernel extends AppKernel
{

    /**
     * 同步请求
     * @param ShellRequest $request
     * @param ShellResponse $response
     */
    public function onSync(
        ShellRequest $request,
        ShellResponse $response
    ): void;

    /**
     * 异步接受响应.
     *
     * @param ShellResponse $response
     */
    public function onAsyncResponse(
        ShellResponse $response
    ) : void;


    /**
     * 异步发送请求.
     * @param ShellRequest $request
     */
    public function onAsyncRequest(
        ShellRequest $request
    ) : void;
}