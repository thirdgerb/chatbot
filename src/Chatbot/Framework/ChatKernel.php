<?php

/**
 * Class ChatbotServerKernel
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Kernel;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Framework\Exceptions\FatalErrorException;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Container\ContainerContract;

/**
 * Class ChatbotKernel
 * @package Commune\Chatbot\Framework
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatKernel implements Kernel
{
    /**
     * @var ChatApp
     */
    protected $app;

    /**
     * @var ChatServer
     */
    protected $server;

    /**
     * @var ExceptionHandler
     */
    protected $expHandler;

    /**
     * ChatbotKernel constructor.
     * @param Application $app
     * @param ChatServer $server
     * @param ExceptionHandler $handler
     */
    public function __construct(
        Application $app,
        ChatServer $server,
        ExceptionHandler $handler
    )
    {
        $this->app = $app;
        $this->server = $server;
        $this->expHandler = $handler;
    }

    public function onUserMessage(MessageRequest $request): void
    {
        try {
            $chatbotConfig = $this->app->getConfig();

            // 初始化这次请求独有的对话容器.
            $conversation = $this->app
                ->getConversationContainer()
                ->onMessage(
                    $request,
                    $chatbotConfig
                );

            // 对对话容器进行boot
            $this->app->bootConversation($conversation);

            $conversation = $this->sendConversationThoughPipe($conversation, $chatbotConfig);

            // 做 destruct 的准备.
            $request->finish();
            $conversation->finish();

        // 理论上不应该在这里出现任何异常.
        } catch (\Throwable $e) {

            $this->app->getConsoleLogger()->critical(strval($e));
            $this->app->setAvailable(false);

            // 直接exit
            $this->server->fail();
        }
    }

    protected function sendConversationThoughPipe(
        Conversation $conversation,
        ChatbotConfig $chatbotConfig
    ) : Conversation
    {
        // 创建pipeline
        $pipeline = $this->buildPipeline(
            $conversation,
            $chatbotConfig->chatbotPipes->onUserMessage
        );

        // 发送会话
        /**
         * @var Conversation $conversation
         */
        return $pipeline->send(
            $conversation,
            function (Conversation $conversation): Conversation {
                return $conversation;
            }
        );
    }

    /**
     * 组装管道
     *
     * @param ContainerContract $container
     * @param array $pipes
     * @return OnionPipeline
     */
    protected function buildPipeline(
        ContainerContract $container,
        array $pipes
    ) : OnionPipeline
    {
        // 关键, 从pipeline 开始, 所有的依赖注入都来自conversation
        $pipeline = new OnionPipeline($container);

        // 中间管道.
        foreach ($pipes as $chatbotPipeName) {
            $pipeline->through($chatbotPipeName);
        }

        // host
        // 返回
        return $pipeline;
    }

}