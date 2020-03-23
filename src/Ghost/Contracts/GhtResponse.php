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

use Commune\Message\Internal\Scope;
use Commune\Message\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @property-read Scope $scope
 */
interface GhtResponse
{

    /*--------- 发送响应 ----------*/

    /**
     * 增加一个需要同步回复的消息.
     * @param Message[] $messages
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
     *
     * @param \Exception|null $e   异常, 会使用异常的 message 和 code. 同时有一个默认值.
     */
    public function sendFailureResponse(\Exception $e = null) : void;


}