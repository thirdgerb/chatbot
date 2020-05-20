<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Request;


use Commune\Protocals\HostMsg;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequest extends AppRequest
{

    /**
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * @param HostMsg $message
     * @param HostMsg ...$messages
     * @return GhostResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages): AppResponse;

    /**
     * @param Cloner $output
     * @param int $errcode
     * @param string $errmsg
     * @return GhostResponse
     */
    public function success(
        $output,
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : AppResponse;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return GhostResponse
     */
    public function fail(int $errcode, string $errmsg = '') : AppResponse;

}