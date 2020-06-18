<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequest extends AppRequest
{

    /**
     * @return GhostResponse|null
     */
    public function validate() : ? GhostResponse;

    /**
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return GhostResponse
     */
    public function response(int $errcode = AppResponse::SUCCESS, string $errmsg = '') : GhostResponse;

    /**
     * @param HostMsg $message
     * @param HostMsg ...$messages
     * @return GhostResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages) : GhostResponse;
}