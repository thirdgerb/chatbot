<?php

/**
 * Class StarterPipe
 * @package Commune\Chatbot\App\ChatPipe\Starter
 */

namespace Commune\Chatbot\App\ChatPipe;



use Carbon\Carbon;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Message\UnsupportedMsg;
use Commune\Chatbot\Blueprint\Pipeline\InitialPipe;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Exceptions;
use Commune\Chatbot\Framework\Pipeline\PipelineLog;
use Psr\Log\LoggerInterface;


/**
 * chatbot 运行管道的第一个环节
 *
 * 主要用于捕获和处理各种异常, 以及抛出事件.
 * 为什么不放到kernel里呢?
 * 因为在 catch 里部分逻辑 (尤其是发送消息) 也可能抛出请求需要针对处理的异常
 *
 * 而 Kernel 是单例, 处理的都是全局异常.
 *
 * Class MessengerPipe
 * @package Commune\Chatbot\App\ChatPipe
 */
class UserMessengerPipe implements InitialPipe
{
    use PipelineLog;

    /**
     * @var ChatServer
     */
    protected $server;

    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UserMessengerPipe constructor.
     * @param ChatServer $server
     * @param ChatbotConfig $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        ChatServer $server,
        ChatbotConfig $config,
        LoggerInterface $logger
    )
    {
        $this->server = $server;
        $this->config = $config;
        $this->logger = $logger;
    }

    /*---------- 接口方法 ----------*/

    /**
     * @return ChatServer
     */
    public function getServer(): ChatServer
    {
        return $this->server;
    }

    public function getPipeName() : string
    {
        return static::class;
    }

    /*---------- 主干流程 ----------*/

    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        $start = new Carbon();
        $traceId = null;
        $request = $conversation->getRequest();

        try {

            // 核心三校验
            // 请求校验. 如果校验不通过, 会话直接结束.
            // 保证最小开销.
            if (!$request->validate()) {
                // 拒绝请求
                $request->sendRejectResponse();
                // 没有任何回复.
                return $this->endConversation($conversation, null);
            }

            // 系统不可用校验
            if (!$this->getServer()->isAvailable()) {
                // 平台不可用.
                // 如果在 Server 实现了这个功能的话.
                $this->replyPlatformUnavailable($conversation);
                $conversation->finishRequest();
                return $this->endConversation($conversation, null);
            }

            // 消息类型校验.
            if ($request->fetchMessage() instanceof UnsupportedMsg) {
                // 提示未处理的消息.
                $this->replyUnsupported($conversation);
                // 发送消息.
                $conversation->finishRequest();
                return $this->endConversation($conversation, $start);
            }

            /**
             * @var Conversation $conversation
             */
            $conversation = $next($conversation);

            // 发送消息.
            $conversation->finishRequest();
            return $this->endConversation($conversation, $start);

        // 跳过所有中间流程. 直接返回 Conversation
        // 可以在管道中使用, 承担一些特殊的功能.
        } catch (Exceptions\ReturnConversationException $e) {
            $conversation->finishRequest();
            return $this->endConversation($conversation, $start);

        // 不是在 validate 环节, 而是其它环节捕获了抛出的异常.
        } catch (Exceptions\RequestException $e) {
            // 禁止继续访问.
            // 默认不记录异常日志, 可以在 sendRejectResponse 中处理.
            $request->sendRejectResponse();
            return $conversation;

        // 对话相关的, 可恢复的异常.
        } catch (Exceptions\ConversationalException $e) {
            return $this->conversationFailure($conversation, $e);

        // 严重到无法用正常消息回复的异常.
        } catch (Exceptions\CloseClientException $e) {
            return $this->closeClient($conversation, $e);

        // 严重到应该重启服务端的异常.
        } catch (Exceptions\CloseServerException $e) {
            return $this->closeServer($conversation, $e);

        // 严重到应该禁止所有服务继续响应的异常.
        } catch (Exceptions\CloseAppException $e) {
            return $this->closeApp($conversation, $e);

        // 其它未被捕获的 runtime exception, 被认为是偶发的.
        } catch (\RuntimeException $e) {
            return $this->conversationFailure(
                $conversation,
                new Exceptions\ConversationalException('Unhandled Runtime Exception', $e)
            );

        // 其它所有异常都应该在管道中处理了.
        // 如果有没处理的异常, 则
        } catch (\Throwable $e) {
            return $this->closeApp(
                $conversation,
                new Exceptions\CloseAppException('Unhandled Throwable Exception', $e)
            );
        }
    }

    /*---------- 特殊异常处理 ----------*/

    /**
     * 可以正常返回响应, 通知用户系统故障.
     *
     * @param Conversation $conversation
     * @param Exceptions\ConversationalException $e
     * @return Conversation
     */
    protected function conversationFailure(
        Conversation $conversation,
        Exceptions\ConversationalException $e
    ) : Conversation
    {
        $this->logger->error($e);
        $this->replySystemFailure($conversation);
        $conversation->finishRequest();
        return $conversation;
    }

    /**
     * 无法正常返回响应. 并关闭客户端.
     *
     * @param Conversation $conversation
     * @param Exceptions\CloseClientException $e
     * @return Conversation
     */
    protected function closeClient(
        Conversation $conversation,
        Exceptions\CloseClientException $e
    ) : Conversation
    {
        $this->logger->error($e);
        $conversation->getRequest()->sendFailureResponse();
        $server = $this->server;
        $conversation->onFinish(function() use ($conversation, $server){
            $this->server->closeClient($conversation);
        });
        return $conversation;
    }

    /**
     * 放弃响应客户端, 直接关闭 Server (或重启)
     *
     * @param Conversation $conversation
     * @param Exceptions\CloseServerException $e
     * @return Conversation
     */
    protected function closeServer(
        Conversation $conversation,
        Exceptions\CloseServerException $e
    ) : Conversation
    {
        $this->logger->critical($e);
        $server = $this->server;
        $conversation->onFinish(function () use ($server) {
            $server->fail();
        });
        return $conversation;
    }


    /**
     * 不仅放弃响应, 试图关闭 Server, 而且试图关闭所有的服务端.
     * 前提是 Server::isAvailable() 有分布式一致性的实现.
     *
     * @param Conversation $conversation
     * @param Exceptions\CloseAppException $e
     * @return Conversation
     */
    protected function closeApp(
        Conversation $conversation,
        Exceptions\CloseAppException $e
    ) : Conversation
    {
        $this->logger->emergency($e);
        $server = $this->server;
        $conversation->onFinish(function() use ($server){
            $server->setAvailable(false);
            $server->fail();
        });
        return $conversation;
    }


    /*---------- 辅助逻辑 ----------*/

    /**
     * 正常完成了请求的返回.
     *
     * @param Conversation $conversation
     * @param Carbon|null $start
     * @return Conversation
     */
    protected function endConversation(
        Conversation $conversation,
        Carbon $start = null
    ) : Conversation
    {
        // 触发结束事件.
        $end = isset($start) ? new Carbon() : null;
        $this->endPipe($conversation, $start, $end);
        return $conversation;
    }

    public function replySystemFailure(Conversation $conversation) : void
    {
        $message = $this->config->defaultMessages->systemError;
        $conversation->reply(new Text($message));
    }

    public function replyPlatformUnavailable(Conversation $conversation) : void
    {
        $message =  $this->config->defaultMessages->platformNotAvailable;
        $conversation->reply(new Text($message));
    }

    public function replyUnsupported(Conversation $conversation) : void
    {
        $message = $this->config->defaultMessages->unsupported;
        $conversation->reply(new Text($message));

    }

}