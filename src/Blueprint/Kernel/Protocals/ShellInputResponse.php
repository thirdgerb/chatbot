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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputResponse extends AppResponse, InputRequest
{
    /**
     * 表示是一个异步的响应, 不会关心请求结果.
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * 如果有回复消息, 则不是一个可以继续往后走的请求.
     * @return bool
     */
    public function hasOutputs() : bool;

    public function getOutputs() : array;
}