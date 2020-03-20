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
interface ChatKernel
{
    /**
     * 接受一个用户的 message, 并完成机器人的回复.
     *
     * @param MessageRequest $request
     */
    public function onUserMessage(MessageRequest $request) : void;

    //public function onMessageToUser(Input $input, Output $output) : void;

    //public function onServiceCallback() : void;
}