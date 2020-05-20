<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host\Router;

use Illuminate\Contracts\Pipeline\Hub;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 可以从收件箱中按以下维度查询消息.
 *
 */
interface GhostRoute
{

    public function getRouteId() : string;

    public function getSessionId() : string;

    public function getConversationId() : string;

    public function newHub() : Hub;

}