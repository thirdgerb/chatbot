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
 * @property-read array $env                    允许传递的环境变量.
 *
 * # 更多
 * @see IntercomMessage
 * @see ShellMsg
 */
interface ShellInput extends IntercomMessage
{

    /**
     * @param string|null $cloneId          为空则使用 ShellId
     * @param string|null $sessionId        为空则使用 Shell 的 SessionId
     * @return GhostInput
     */
    public function toGhostInput(
        string $cloneId = null,
        string $sessionId = null
    ) : GhostInput;
}