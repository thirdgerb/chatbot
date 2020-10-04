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
 * 获取 Packer, 将 packer 的数据转化为 Commune 所需要的 AppRequest
 * 并将 AppResponse 解析成 packer
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Adapter
{

    /**
     * 请求是否异常检查.
     * @return null|string
     */
    public function isInvalidRequest() : ? string;

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