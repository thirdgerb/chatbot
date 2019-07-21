<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Container\ContainerContract;

interface ConversationContainer extends ContainerContract
{

    /**
     * 获取 process 级别的主容器
     * @return ContainerContract
     */
    public function getProcessContainer() : ContainerContract;

    /**
     * 系统基本配置
     * @return ChatbotConfig
     */
    public function getChatbotConfig() : ChatbotConfig;

    /**
     * 事件调度器
     * @return EventDispatcher
     */
    public function getEventDispatcher() : EventDispatcher;

    /*------------ create ------------*/

    /**
     * 消息来唤起一个conversation
     *
     * @param MessageRequest $request
     * @param ChatbotConfig $config
     * @return Conversation
     */
    public function onMessage(MessageRequest $request, ChatbotConfig $config) : Conversation;

}