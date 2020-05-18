<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Request;

use Commune\Protocals\HostMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Protocal\Protocal;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppRequest extends Protocal
{

    /**
     * @return bool
     */
    public function isValid() : bool;

    /**
     * 无状态请求
     * @return bool
     */
    public function isStateless() : bool;

    /**
     * @return bool
     */
    public function noOutputs() : bool;

    /**
     * @return IntercomMsg
     */
    public function getInput() : IntercomMsg;

    /**
     * @param HostMsg $message
     * @param HostMsg ...$messages
     * @return AppResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages) : AppResponse;

    /**
     * @param $output
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function response(
        $output,
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : AppResponse;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function fail(int $errcode, string $errmsg = '') : AppResponse;

}