<?php

/**
 * Class ChatbotServerKernel
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\ChatKernel;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ExceptionReporter;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Container\ContainerContract;
use Psr\Log\LogLevel;

/**
 * 响应对话请求的内核.
 * 理论上所有的对话请求都由它来响应. 目前仅有 Message Request
 */
class ChatKernelImpl implements ChatKernel
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
     * @var ExceptionReporter
     */
    protected $expReporter;

    protected $booted = false;

    /**
     * ChatbotKernel constructor.
     * @param Application $app
     * @param ChatServer $server
     * @param ExceptionReporter $handler
     */
    public function __construct(
        Application $app,
        ChatServer $server,
        ExceptionReporter $handler
    )
    {
        $this->app = $app;
        $this->server = $server;
        $this->expReporter = $handler;
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

            // 在管道中运行.
            $conversation = $this->sendConversationThoughPipe($conversation, $chatbotConfig);


            // 注意, 中间环节没有任何逻辑.
            // 所有逻辑包括请求发送等, 都应该在 chatbot pipe 中执行.

            // 做 destruct 的准备.
            $request->finish();
            $conversation->finish();

        // 理论上不应该在这里出现任何异常. 应该都在 ChatbotPipe 中拦截了.
        // 如果这个环节出现异常, 系统就不得不重启了.
        } catch (\Throwable $e) {
            $this->expReporter->report(
                LogLevel::EMERGENCY,
                $e
            );
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