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
use Commune\Protocals\IntercomMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Comprehension $comprehension  对输入消息的抽象理解.
 * @property-read string $sceneId               场景Id, 决定启动时的 Context
 * @property-read array $env                    允许传递的环境变量.
 *
 * # 更多
 * @see IntercomMsg
 * @see ShellMsg
 */
interface ShellInput extends IntercomMsg
{

    /*----- 额外的信息 -----*/

    public function getSceneId() : string;

    public function getEnv() : array;

    public function getComprehension() : Comprehension;

    /*----- 转换类方法 -----*/

    /**
     *
     * @param string|null $cloneId
     * @param string|null $sessionId
     * @param string|null $guestId
     * @return GhostInput
     */
    public function toGhostInput(
        string $cloneId = null,
        string $sessionId = null,
        string $guestId = null
    ) : GhostInput;
}