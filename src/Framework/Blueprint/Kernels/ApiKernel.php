<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Kernel;

use Commune\Shell\Platform\Request;
use Commune\Shell\Platform\Response;


/**
 * 处理各种无状态的 API 请求. 仍然能对对话状态产生干涉.
 * 只有同步逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiKernel
{

    /**
     * 同步响应一个 API 请求.
     *
     * @param Request $request
     * @param Response $response
     */
    public function onApiRequest(
        Request $request,
        Response $response
    ) : void;

}