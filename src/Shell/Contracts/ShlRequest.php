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
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlRequest
{

    /**
     * 在平台上可以有自己的请求校验策略.
     * 校验失败自行返回结果.
     * @return bool
     */
    public function validate() : bool ;

    /**
     * 关于请求的描述. 通常用于日志.
     * @return string
     */
    public function getBrief() : string;

    /**
     * 获取应该存到日志里的信息
     * @return array
     */
    public function getLogContext() : array;

    /*-------- 必须有的参数 --------*/

    /**
     * 请求原始的输入信息.
     * @return mixed
     */
    public function getInput();


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
     * @return string
     */
    public function getTraceId() : string;

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