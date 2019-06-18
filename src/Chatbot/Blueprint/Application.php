<?php

/**
 * Class ChatbotApp
 * @package Commune\Chatbot\Blueprint
 */

namespace Commune\Chatbot\Blueprint;

use Commune\Chatbot\Blueprint\Conversation\ConversationContainer;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Exceptions\BootingException;
use Commune\Container\ContainerContract;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Psr\Log\LoggerInterface;

interface Application
{

    /*----------- 预绑定组件 ------------*/

    /**
     * 启动时, 系统专用的日志模块.
     * 在console 里打印日志.
     *
     * @return ConsoleLogger
     */
    public function getConsoleLogger(): ConsoleLogger;

    /**
     * 系统默认的日志.
     *
     * @return ChatbotConfig
     */
    public function getConfig(): ChatbotConfig;

    /*----------- container ------------*/

    /**
     * Reactor 的 IoC 容器
     * @return ContainerContract
     */
    public function getReactorContainer() : ContainerContract;

    /**
     * Conversation 的 IoC 容器
     * @return ConversationContainer
     */
    public function getConversationContainer() : ConversationContainer;


    /*----------- 注册 ------------*/

    /**
     * @throws BootingException
     */
    public function bootReactor() : Application;

    /**
     * 使用 ServiceProvider 注册 Reactor 的服务
     * @param string $provider
     */
    public function registerReactorService(string $provider) : void;

    /**
     * 使用 ServiceProvider 注册 Conversation 的服务
     * @param string|ServiceProvider $provider
     */
    public function registerConversationService($provider) : void;

    /**
     * 初始化 Conversation 注册的服务.
     * @param Conversation $conversation
     */
    public function bootConversation(Conversation $conversation) : void;

    /*----------- 运行 ------------*/

    /**
     * 获取系统的 kernel
     * @return Kernel
     */
    public function getKernel() : Kernel;

    public function getServer() : ChatServer;

    /*----------- 状态 ------------*/

    public function setAvailable(bool $status) : void;

    public function isAvailable() : bool ;


}