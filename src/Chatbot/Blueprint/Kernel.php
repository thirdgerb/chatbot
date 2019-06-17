<?php

/**
 * Class ChatbotKernel
 * @package Commune\Chatbot\Blueprint
 */

namespace Commune\Chatbot\Blueprint;


use Commune\Chatbot\Blueprint\Conversation\MessageRequest;

/**
 * Interface Kernel
 * @package Commune\Chatbot\Blueprint
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * chatbot 的 kernel. 用于执行各种允许的逻辑.
 */
interface Kernel
{

    // 用户发送来的消息
    const ON_USER_MESSAGE = 'onUserMessage';
    // 接受到给用户的异步回复消息
    const ON_REPLY_MESSAGE = 'onReplyMessage';
    // 得到了一个服务的异步回调.
    const ON_SERVICE_MESSAGE = 'onServiceMessage';

    /**
     * 接受一个用户的 message, 并完成机器人的回复.
     *
     * @param MessageRequest $request
     */
    public function onUserMessage(MessageRequest $request) : void;

    //public function onMessageToUser(Input $input, Output $output) : void;

    //public function onServiceCallback() : void;
}