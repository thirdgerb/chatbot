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

use Commune\Framework\Blueprint\Conversation\Comprehension;
use Commune\Message\Blueprint\ConvoMsg;

/**
 * 平台上的同步请求
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Request
{

    /**
     * 请求的场景
     * @return string
     */
    public function getScene() : string;

    /**
     * 当前场景的环境变量.
     * @return array
     */
    public function getSceneEnv() : array;

    /**
     * 在平台上可以有自己的请求校验策略.
     * 校验失败自行返回结果.
     * @return bool
     */
    public function validate() : bool ;

    /*-------- 必须有的参数 --------*/

    /**
     * 请求原始的输入信息.
     * @return mixed
     */
    public function getInput();

    /**
     * 从 input 中获取 message
     *
     * fetch message from request input
     *
     * @return ConvoMsg
     */
    public function fetchMessage() : ConvoMsg;

    /**
     * 从 input 中获取消息ID, 或者生成一个ID.
     * 需要生成 ID 时, 应当调用 generateMessageId() 方法
     *
     * fetch message id from request input, or generate one
     *
     * @return string
     */
    public function fetchMessageId() : string;

    /**
     * 发送消息的用户 ID
     *
     * @return string
     */
    public function fetchUserId() : string;


    /*-------- 可能有的参数 --------*/

    /**
     * 请求内已经包含的高级抽象理解.
     *
     * @return Comprehension|null
     */
    public function fetchComprehension() : ? Comprehension;

    /**
     * 请求给出的 sessionId
     * @return null|string
     */
    public function fetchSessionId() : ? string;


    /**
     * 从请求里获得的 chatId
     * @return null|string
     */
    public function fetchChatId() : ? string;

}