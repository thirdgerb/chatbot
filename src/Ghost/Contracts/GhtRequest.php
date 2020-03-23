<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Contracts;

use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Internal\IncomingMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhtRequest
{

    /**
     * 检查请求是否合法
     * @return bool
     */
    public function validate() : bool;

    /**
     * 从请求中获取 IncomingMsg
     * @return IncomingMsg
     */
    public function fetchIncoming() : IncomingMsg;

    /*--------- 发送响应 ----------*/

    /**
     * 增加一个需要同步回复的消息.
     * @param ConvoMsg[] $messages
     */
    public function buffer(array $messages) : void;

    /**
     * 将当前准备要发送的信息, 全部发送给用户.
     *
     * send all messages from buffer and clear buffer
     */
    public function sendResponse() : void;

    /**
     * 告知请求不合法. 这样的信息不走机器人, 直接拒绝掉.
     */
    public function sendRejectResponse() : void;

    /**
     * 系统响应失败, 而且无法用消息管道通知用户.
     * 通常因为异常导致.
     */
    public function sendFailureResponse() : void;

}