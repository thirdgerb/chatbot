<?php

/**
 * Class Request
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Blueprint\Message\Message;

/**
 * conversation request from user message input
 */
interface MessageRequest
{

    public function withConversation(Conversation $conversation) : void;

    /**
     * origin input
     *
     * @return mixed
     */
    public function getInput();

    /*-------- generate --------*/

    /**
     * 生成一个消息ID.
     * 通常用于回复.
     * 按平台自己的意愿去生成. 可能平台自己有套规矩.
     *
     * generate message id.
     * usually for reply
     *
     * @return string
     */
    public function generateMessageId() : string;

    /*-------- predefined --------*/

    /**
     * fetch chatbot name
     * @return string
     */
    public function getChatbotName() : string ;

    /**
     * 获取平台的唯一标示. 比如 wechat.
     *
     * platform name
     *
     * @return string
     */
    public function getPlatformId() : string;

    /*-------- fetch message from request --------*/

    /**
     * 从 input 中获取 message
     *
     * fetch message from request input
     *
     * @return Message
     */
    public function fetchMessage() : Message;

    /**
     * 从 input 中获取消息ID, 或者生成一个ID, 不变.
     *
     * fetch message id from request input, or generate one
     *
     * @return string
     */
    public function fetchMessageId() : string;

    /**
     * 获取 trace id, 可能是生成的, 也可能是从input中继承的. 方便上下文追踪.
     *
     * usually traceId is MessageId
     * @return string
     */
    public function fetchTraceId() : string;

    /**
     * if could not fetch chat id from request
     * then conversation will generate chat id with userId, platformId and chatbotName
     *
     * @return null|string
     */
    public function fetchChatId() : ? string;

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

    /*-------- sending --------*/

    /**
     * buffer 一个需要发送的消息.
     * 可能会导致直接发送.
     *
     * buffer sending message
     *
     * maybe directly sending if platform is duplex
     *
     * @param ConversationMessage $message
     */
    public function bufferConversationMessage(ConversationMessage $message) : void;

    /**
     * 将当前准备要发送的信息, 全部发送给用户.
     *
     * send all messages from buffer and clear buffer
     */
    public function flushChatMessages() : void;

    /*-------- finish --------*/

    /**
     * 完成一次请求.
     *
     * complete request and do some cleanup
     */
    public function finishRequest() : void;



}