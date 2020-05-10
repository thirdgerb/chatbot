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