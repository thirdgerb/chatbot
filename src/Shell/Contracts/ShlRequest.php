<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Contracts;

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlRequest extends Request
{

    /**
     * 请求的场景
     * @return string
     */
    public function getSceneId() : string;

    /**
     * 当前场景的环境变量.
     * @return array
     */
    public function getSceneEnv() : array;

    /**
     * 从 input 中获取 message
     *
     * fetch message from request input
     *
     * @return Message
     */
    public function getMessage() : Message;

    /**
     * 从 input 中获取消息ID, 或者生成一个ID.
     * 需要生成 ID 时, 应当调用 generateMessageId() 方法
     *
     * @return string
     */
    public function getMessageId() : string;

    /**
     * 发送消息的用户 ID
     *
     * @return string
     */
    public function getUserId() : string;

    /**
     * 请求内已经包含的高级抽象理解.
     *
     * @return null|Comprehension
     */
    public function getComprehension() : ? Comprehension;

    /**
     * 从请求中给出 ChatId, 是 shell 内部的 chatId
     * @return null|string
     */
    public function getChatId() : ? string;

    /**
     * 请求给出的 sessionId, 是 Shell 内部的 SessionId
     * @return null|string
     */
    public function getSessionId() : ? string;


}