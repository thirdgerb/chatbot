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

use Commune\Protocals\IntercomMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostResponse extends AppResponse
{

    /**
     * 消息的批次 ID
     * @return string
     */
    public function getBatchId() : string;

    /**
     * 异步的消息, shell 不用渲染.
     * 免得增加逻辑上的复杂度.
     *
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * Shell 的名称.
     * @return string
     */
    public function getShellName() : string;

    /**
     * 对应 Shell 的 Session
     * @return string
     */
    public function getShellSessionId() : string;

    /**
     * @return int
     */
    public function getMessageCount() : int;

    /**
     * 没有消息体的回复.
     * @return bool
     */
    public function isTinyResponse() : bool;

    /**
     * 获取响应中的消息. 不一定携带消息.
     * @return IntercomMsg[]
     */
    public function getMessages() : array;

}