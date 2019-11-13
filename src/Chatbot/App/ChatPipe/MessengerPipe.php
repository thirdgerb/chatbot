<?php

/**
 * Class StarterPipe
 * @package Commune\Chatbot\App\ChatPipe\Starter
 */

namespace Commune\Chatbot\App\ChatPipe;



use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Message\UnsupportedMsg;
use Commune\Chatbot\Blueprint\Pipeline\InitialPipe;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\Framework\Pipeline\PipelineLog;



/**
 * chatbot 运行管道的第一个环节
 * 主要用于捕获和处理各种异常, 以及抛出事件.
 * 为什么不放到kernel里呢?
 * 因为在 catch 里部分逻辑 (尤其是发送消息) 也可能抛出致命异常
 * 如果在kernel里, 就没办法捕获到这些致命异常.
 *
 * Class MessengerPipe
 * @package Commune\Chatbot\App\ChatPipe
 */
class MessengerPipe implements InitialPipe
{
    use PipelineLog;

    /**
     * @var Application
     */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return ChatServer
     */
    public function getServer(): ChatServer
    {
        return $this->app->getProcessContainer()[ChatServer::class];
    }

    public function getPipeName() : string
    {
        return static::class;
    }

    public function getExpHandler() : ExceptionHandler
    {
        return $this->app->getProcessContainer()[ExceptionHandler::class];
    }

    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        $start = new Carbon();
        $traceId = null;
        $request = $conversation->getRequest();

        // 核心三校验
        try {

            // 如果校验不通过, 会话直接结束.
            if (!$request->validate()) {
                // 拒绝请求
                $request->sendRejectResponse();
                return $this->receiveConversation($conversation, null, false);
            }

            // 系统不可用.
            if (!$this->app->isAvailable()) {
                $this->replyUnavailable($conversation);
                return $this->receiveConversation($conversation, null);
            }

            // 消息类型不支持.
            if ($request->fetchMessage() instanceof UnsupportedMsg) {
                $this->replyUnsupported($conversation);
                return $this->receiveConversation($conversation, $start);
            }

            // 启动
            $this->startPipe($conversation);

            /**
             * @var Conversation $conversation
             */
            $conversation = $next($conversation);

            return $this->receiveConversation($conversation, $start);

        // 逻辑上的异常. 偶发. 可以继续响应.
        } catch (LogicException $e) {
            $handler = $this->getExpHandler();
            $handler->reportException($conversation, $e);

            $this->replyLogicFailure($conversation);
            return $this->receiveConversation($conversation, $start);

        // 无法对用户进行响应的异常.
        } catch (\Throwable $e) {

            $handler = $this->getExpHandler();
            $handler->reportException($conversation, $e);

            $conversation = $this->getExpHandler()->handleException($conversation, $e);
            return $this->receiveConversation($conversation, $start, false);

        }
    }

    public function receiveConversation(
        Conversation $conversation,
        Carbon $start = null,
        bool $isNormalResponse = true
    ) : Conversation
    {
        // 结束
        if (isset($start)) {
            $this->endPipe($conversation, $start, new Carbon());
        }
        if ($isNormalResponse) {
            $conversation->finishRequest();
        }
        return $conversation;
    }

    public function replyLogicFailure(Conversation $conversation) : void
    {
        $conversation->getSpeech()->error(
            $this->app
                ->getConfig()
                ->defaultMessages
                ->systemError
        );
    }


    public function replyUnsupported(Conversation $conversation) : void
    {
        $conversation->getSpeech()->warning(
            $this->app
                ->getConfig()
                ->defaultMessages
                ->unsupported
        );

    }

    public function replyUnavailable(Conversation $conversation) : void
    {
        $conversation->getSpeech()->error(
            $this->app
                ->getConfig()
                ->defaultMessages
                ->platformNotAvailable
        );
    }

}