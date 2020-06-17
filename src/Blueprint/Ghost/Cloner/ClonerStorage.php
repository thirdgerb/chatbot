<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;

use Commune\Blueprint\Framework\Session\SessionStorage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property int|null $requestFailTimes     请求异常计数. 超过计数后会退出会话.
 * @property string[] $shellSessionRoutes   Shell2Ghost 的路由表. 记录 ShellName => ShellSessionId
 */
interface ClonerStorage extends SessionStorage
{
}