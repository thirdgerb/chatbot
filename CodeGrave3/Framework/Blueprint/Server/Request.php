<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Server;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Request
{
    /**
     * 是否是无状态的请求.
     * @return bool
     */
    public function isStateless() :bool;

    /**
     * 在平台上可以有自己的请求校验策略.
     * 校验失败自行返回结果.
     * @return bool
     */
    public function validate() : bool;

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

    /**
     * 请求原始的输入信息.
     * @return mixed
     */
    public function getInput();

    /**
     * 链路的追踪ID
     * @return string
     */
    public function getTraceId() : string;


    /**
     * 请求的唯一ID.
     * @return string
     */
    public function getUuid() : string;

    /**
     * @return string
     */
    public function getChatId() : string;

    /**
     * 发送消息的用户 ID
     *
     * @return string
     */
    public function getUserId() : string;

    /**
     * 从 input 中获取消息ID, 或者生成一个ID.
     * 需要生成 ID 时, 应当调用 generateMessageId() 方法
     *
     * @return string
     */
    public function getMessageId() : string;

    /**
     * @return string
     */
    public function getSceneId() : ? string;



}