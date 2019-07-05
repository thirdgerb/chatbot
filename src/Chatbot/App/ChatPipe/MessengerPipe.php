<?php

/**
 * Class StarterPipe
 * @package Commune\Chatbot\App\ChatPipe\Starter
 */

namespace Commune\Chatbot\App\ChatPipe;



use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Pipeline\InitialPipe;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\Framework\Exceptions\ConversationalException;
use Commune\Chatbot\Framework\Exceptions\FatalErrorException;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
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
        return $this->app->getReactorContainer()[ChatServer::class];
    }

    public function getPipeName() : string
    {
        return static::class;
    }

    public function getExpHandler() : ExceptionHandler
    {
        return $this->app->getReactorContainer()[ExceptionHandler::class];
    }

    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        $start = new Carbon();
        $traceId = null;
        try {

            if (!$this->app->isAvailable()) {
                $this->replyUnavailable($conversation);
                return $conversation;
            }

            // 启动
            $this->startPipe($conversation, $start);

            /**
             * @var Conversation $conversation
             */
            $conversation = $next($conversation);
            return $this->receiveConversation($conversation, $start);

        // 需要将异常内的消息发送给用户
        // 一种跳出流程的方式.
        } catch (ConversationalException $e) {

            return $this->receiveConversation(
                $e->getConversation(),
                $start
            );

        // kill 掉当前的会话, 仍然允许 server 继续运行
        } catch (RuntimeException $e) {

            $this->getExpHandler()->reportRuntimeException(__METHOD__, $e);
            $this->getServer()->closeClient($conversation);

            // 抛出后会关闭当前客户端.
            // 所以在此之前抛出事件.
            return $conversation;

        // 致命的错误
        } catch (FatalErrorException $e) {

            $this->getExpHandler()->reportServiceStopException(__METHOD__, $e);
            $this->app->setAvailable(false);
            $this->getServer()->fail();

            // 如果下游抛出了致命异常
            // 直接到上层, 关闭掉chatbotApp
            return $conversation;

        // 不是允许的异常, 则抛到上一层. 关闭掉客户端.
        } catch (\Exception $e) {

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
        // 发送消息.
        $request = $conversation->getRequest();
        $request->flushChatMessages();
        // 这一步.
        $request->finishRequest();

        return $conversation;
    }

    public function replyUnavailable(Conversation $conversation)
    {
        $conversation->monolog()->error(
            $this->app
                ->getConfig()
                ->defaultMessages
                ->platformNotAvailable
        );
    }

}