<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Protocals\Comprehension;
use Commune\Protocals\IntercomMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Comprehension $comprehension  对输入消息的抽象理解.
 * @property-read string $sceneId               场景Id, 决定启动时的 Context
 *
 * # 更多
 * @see IntercomMessage
 * @see ShellMsg
 */
interface ShellInput extends IntercomMessage
{
}