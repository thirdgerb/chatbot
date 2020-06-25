<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Adapter
{

    /**
     * 异常检查.
     * @return null|string
     */
    public function isInvalid() : ? string;

    /**
     * @return AppRequest
     */
    public function getRequest() : AppRequest;

    /**
     * @param AppResponse $response
     */
    public function sendResponse(AppResponse $response) : void;

    /**
     * 为方便垃圾回收, 主动清除绑定.
     */
    public function destroy() : void;
}