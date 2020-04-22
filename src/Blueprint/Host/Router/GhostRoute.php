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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 可以从收件箱中按以下维度查询消息.
 *
 * @property-read string $id            根据下面三者算出来的一个 ID
 *
 * @property-read string $cloneId
 * @property-read string $sessionId
 */
interface GhostRoute
{
}