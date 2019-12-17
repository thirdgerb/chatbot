<?php

namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\RequestException;

/**
 * conversation request from user message input
 */
interface MessageRequest
{
    /*------- 系统方法 --------*/

    /**
     * request 和 conversation 合体时调用.
     * @param Conversation $conversation
     */
    public function withConversation(Conversation $conversation) : void;


    /**
     * 在平台上可以有自己的请求校验策略.
     * 校验失败自行返回结果.
     * @return bool
     */
    public function validate() : bool ;

    /**
     * origin input
     *
     * @return mixed
     */
    public function getInput();


    /**
     * 获取场景名称. 对一个 chatbot 而言, 可能有多个不同的请求入口, 称之为场景.
     * 每一个已定义的场景, 都能拥有一个独立的session.
     * 如果在 hostConfig->sceneContextNames 里有定义, 会该 context name 作为根路径, 并进入一个独立的 session.
     *
     * 用这种方式可以快速实现同一个机器人, 多场景分别启动. 而不用配置很多套相同的机器人.
     *
     * @return null|string
     */
    public function getScene() : ? string;

    /**
     * 获取需要记录到日志里的参数. 会传递到 conversation logger
     * 方便排查端上的问题. 理论上只记录有维度价值的参数, 太多的话也不利于记录日志.
     * 注意这一步不应该抛出异常.
     *
     * @return array
     */
    public function getLogContext() : array;

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
     * 获取 trace id, 可能是生成的, 也可能是从input中继承的. 方便上下文追踪.
     *
     * usually traceId is MessageId
     * @return string
     */
    public function fetchTraceId() : string;


    /**
     * @return null|string
     */
    public function fetchSessionId() : ? string;

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
    public function bufferMessage(ConversationMessage $message) : void;

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

    /*-------- finish --------*/

    /**
     * 完成请求. 
     * 所有消息应该在这一步完成最终处理.
     * 清理掉可能造成内存泄露的属性.
     *
     * complete request and do some cleanup
     */
    public function finish() : void;



}