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

use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CloneResponse extends AppResponse
{

    /**
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * 要求极简的回复, 不需要消息体.
     * @return bool
     */
    public function requireTinyResponse() : bool;

    /**
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * @return InputMsg[]
     */
    public function getAsyncInputs() : array;

    /**
     * @return OutputMsg[]
     */
    public function getOutputs() : array;


    /**
     * 给所有的输出设置 convoId
     * @param string $convoId
     */
    public function setConvoId(string $convoId) : void;
}