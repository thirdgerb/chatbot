<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Session;

use Commune\Blueprint\Framework\Session\SessionStorage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string|null $cloneSessionId    Shell 所要访问 Ghost 的 CloneSessionId. 如果存在, 会替换掉当前 sessionId 去访问 Ghost.
 */
interface ShellStorage extends SessionStorage
{

}