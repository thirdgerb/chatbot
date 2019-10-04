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
use Commune\Chatbot\Framework\Exceptions\FatalErrorException;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\Framework\Pipeline\PipelineLog;
use Commune\Chatbot\Blueprint\Exceptions\RequestExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;



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

        // 核心三校验
        try {

            // 如果校验不通过, 会话直接结束.
            $request = $conversation->getRequest();
            if (!$request->validate()) {
                return $this->receiveConversation($conversation, $start);
            }

            // 系统不可用.
            if (!$this->app->isAvailable()) {
                $this->replyUnavailable($conversation);
                return $this->receiveConversation($conversation, $start);
            }

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

        // 偶发的逻辑错误, 无法对用户进行响应了.
        } catch (RequestExceptionInterface $e) {
            $conversation->getLogger()->error(strval($e));
            return $conversation;

        // kill 掉当前的会话, 仍然允许 server 继续运行
        } catch (RuntimeExceptionInterface $e) {
            // 抛出后会关闭当前客户端.
            $this->getExpHandler()->reportRuntimeException(__METHOD__, $e);
            $this->getServer()->closeClient($conversation);
            return $conversation;

        // 致命的错误
        } catch (StopServiceExceptionInterface $e) {

            $this->getExpHandler()->reportServiceStopException(__METHOD__, $e);
            $this->app->setAvailable(false);
            $this->getServer()->fail();

            // 如果下游抛出了致命异常
            // 直接到上层, 关闭掉chatbotApp
            return $conversation;

        // 不是允许的异常, 则抛到上一层. 关闭掉客户端.
        } catch (\Throwable $e) {

            $re = new FatalErrorException( $e);

            $this->getExpHandler()->reportServiceStopException(__METHOD__, $re);
            $this->app->setAvailable(false);
            $this->getServer()->fail();
            return $conversation;
        }
    }

    public function receiveConversation(
        Conversation $conversation,
        Carbon $start
    ) : Conversation
    {
        // 结束
        $this->endPipe($conversation, $start, new Carbon());
        $conversation->finishRequest();
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
                ->platformNotAvailable
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