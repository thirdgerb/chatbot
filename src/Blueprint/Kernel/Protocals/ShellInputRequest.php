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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputRequest extends AppRequest
{

    /**
     * 可以是不同类型的 response. 方便进行处理.
     *
     * @param int $errcode
     * @param string $errmsg
     * @return ShellOutputResponse
     */
    public function fail(int $errcode, string $errmsg = '') : ShellOutputResponse;

    /**
     * @return ShellOutputResponse
     */
    public function noContent() : ShellOutputResponse;

    /**
     * @param HostMsg $message
     * @param HostMsg[] $messages
     * @return ShellInputResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages) : ShellInputResponse;
}