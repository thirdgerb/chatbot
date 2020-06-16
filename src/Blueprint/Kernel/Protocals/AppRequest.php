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
interface AppRequest extends AppProtocal
{

    /**
     * @return bool
     */
    public function isValid() : bool;

    /**
     * 如果不合法, 给出错误提示.
     * @return null|string
     */
    public function isInvalid() : ? string;

    /**
     * @return bool
     */
    public function isStateless() : bool;

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
     * @return AppResponse
     */
    public function noContent();

    /**
     * @param HostMsg $message
     * @param HostMsg[] $messages
     * @return AppResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages);

}