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
use Commune\Protocals\Intercom\InputMsg;
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
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function response(int $errcode, string $errmsg = '');

}