<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform;

use Commune\Messages\Blueprint\ConvoMsg;
use Commune\Framework\Blueprint\Chat\ChatScope;

/**
 * 平台上的同步请求
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Request
{
    /**
     * origin input
     *
     * @return mixed
     */
    public function getInput();

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

    /**
     * 获取需要记录到日志里的参数. 会传递到 conversation logger
     * 方便排查端上的问题. 理论上只记录有维度价值的参数, 太多的话也不利于记录日志.
     * 注意这一步不应该抛出异常.
     *
     * @return array
     */
    public function getLogContext() : array;

    /*-------- messages --------*/

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
     * fetch nlu information from request
     * return null if request don't has any nature language information
     *
     * @return NLU|null
     */
    public function fetchNLU() : ? NLU;


    /**
     * @return null|string
     */
    public function fetchSessionId() : ? string;

    /*-------- chat  --------*/

    public function getChatInfo() : ChatScope;

    /*-------- fetch user from request --------*/

    /**
     * user id from request
     *
     * @return string
     */
    public function fetchUserId() : string;

    /**
     * user name from request
     * @return string
     */
    public function fetchUserName() : string;

    /**
     * user origin data from request
     * @return array
     */
    public function fetchUserData() : array;






}