<?php

/**
 * Class Request
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Blueprint\Message\Message;

interface MessageRequest
{
    /*-------- generate --------*/

    /**
     * 生成一个消息ID.
     * 通常用于回复.
     * 按平台自己的意愿去生成. 可能平台自己有套规矩.
     * @return string
     */
    public function generateMessageId() : string;

    /*-------- predefined --------*/

    /**
     * 获取自身的ID. 可能是写死的, 可能是平台传递来的.
     * @return string
     */
    public function getChatbotUserId() : string ;

    /**
     * 获取平台的唯一标示. 比如 wechat.
     * @return string
     */
    public function getPlatformId() : string;

    /*-------- fetch message from request --------*/

    /**
     * 从 input 中获取 message
     * @return Message
     */
    public function fetchMessage() : Message;

    /**
     * 从 input 中获取消息ID, 或者生成一个ID, 不变.
     * @return string
     */
    public function fetchMessageId() : string;

    /**
     * 获取 trace id, 可能是生成的, 也可能是从input中继承的. 方便上下文追踪.
     * @return string
     */
    public function fetchTraceId() : string;


    /*-------- fetch user from request --------*/

    /**
     * 发送者的ID
     * 之所以分三个方法, 因为不同平台的获取方式完全不一样.
     * @return string
     */
    public function fetchUserId() : string;

    /**
     * 发送者的名称.
     * @return string
     */
    public function fetchUserName() : string;

    /**
     * 发送者信息. 考虑可能取不到, 可能取起来麻烦.
     * @return array
     */
    public function fetchUserData() : array;

    /*-------- sending --------*/

    /**
     * buffer 一个需要发送的消息.
     * 可能会导致直接发送.
     *
     * @param ConversationMessage $message
     */
    public function bufferMessageToChat(ConversationMessage $message) : void;

    /**
     * 将当前准备要发送的信息, 全部发送给用户.
     */
    public function flushChatMessages() : void;

    /*-------- finish --------*/

    /**
     * 完成一次请求.
     */
    public function finishRequest() : void;



}