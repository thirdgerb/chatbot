<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Platform;

use Commune\Message\Blueprint\ConvoMsg;
use Commune\Shell\Exceptions\RequestException;

/**
 * 平台上的同步响应
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Response
{

    /**
     * 增加一个需要同步回复的消息.
     * @param ConvoMsg[] $messages
     */
    public function buffer(array $messages) : void;

    /**
     * 将当前准备要发送的信息, 全部发送给用户.
     *
     * send all messages from buffer and clear buffer
     *
     * @throws RequestException
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