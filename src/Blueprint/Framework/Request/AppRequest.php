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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppRequest extends AppProtocal
{
    /*------- 状态 -------*/

    /**
     * @return bool
     */
    public function isValid() : bool;

    /**
     * @return null|string  error notice
     */
    public function isInvalid() : ? string;

    /**
     * 无状态请求
     * @return bool
     */
    public function isStateless() : bool;

    /*------- 关键维度 -------*/

    /**
     * 请求的唯一 ID
     * @return string
     */
    public function getRequestId() : string;

    /*------- 参数 -------*/

    /**
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * 可以是不同类型的 response. 方便进行处理.
     *
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function fail(int $errcode, string $errmsg = '');

    /**
     * @param HostMsg $message
     * @param HostMsg[] $messages
     * @return AppResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages);
}