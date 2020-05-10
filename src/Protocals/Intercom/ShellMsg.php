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

use Commune\Protocals\IntercomMsg;


/**
 * Shell 上传输和处理的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellMsg extends IntercomMsg
{

    public function getShellId() : string;

    public function getShellName() : string;

    public function getSenderId() : string;

    public function getSessionId() : ? string;

}