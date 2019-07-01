<?php

/**
 * Class Conversation
 * @package Commune\Chatbot\Framework\Converstaion
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Blueprint\Conversation\Ability;
use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Conversation\Conversation as Blueprint;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Conversation\Signal;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Commune\Chatbot\Framework\Events\ChatbotPipeStart;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Exceptions\FatalErrorException;
use Commune\Container\ContainerContract;
use Commune\Container\RecursiveContainer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class Conversation
 * 用容器来实现会话. 缺点是不能序列化, 反序列化.
 *
 * 使用 静态变量容器, 绑定在静态变量中, 容器实例通用
 * 但单例 (singleton 与 share )仅为容器持有.
 * 这么做的目的是在同一个进程中隔离不同的请求.
 *
 * 当使用协程模式时, 就非常方便了, 可以通过容器实例获取各种请求级的实例
 * 然后在请求结束时自动销毁.
 *
 * 基于IoC 容器, 可以实现各种双向绑定.
 *
 */
class ConversationImpl implements Blueprint
{
    use RecursiveContainer;

    protected static $ids = [];

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var string
     */
    protected $incomingMessageId;

    /**
     * @var Message[]
     */
    protected $replies = [];

    /**
     * @var OutgoingMessageImpl[]
     */
    protected $replyMessages = [];

    /**
     * @var bool
     */
    protected $asConversation = false;

    /**
     * @var \Closure[]
     */
    protected $finishCallers = [];

    public function onMessage(MessageRequest $request, ChatbotConfig $config): Blueprint
    {
        $container = new static($this->parentContainer);

        // 提高效率
        $container->share(ChatbotConfig::class, $config);

        // 绑定自身.
        $container->share(Conversation::class, $container);

        // share request
        $container->share( MessageRequest::class, $request);
        $container->share(get_class($request), $request);

        $container->asConversation = true;
        $trace = $container->getTraceId();
        self::$ids[$trace] = $container->getConversationId();
        return $container;
    }

    public function getReactorContainer(): ContainerContract
    {
        return $this->getParentContainer();
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->make(EventDispatcher::class);
    }

    public function fire(Event $event): void
    {
        $this->getEventDispatcher()->dispatch($event);
    }

    public function getLogger(): ConversationLogger
    {
        return $this->make(ConversationLogger::class);
    }

    /*------ status ------*/


    public function isInstanced() : bool
    {
        return $this->asConversation;
    }

    public static function getInstanceIds(): array
    {
        return self::$ids;
    }

    public function isAbleTo(string $abilityInterfaceName): bool
    {
        if (!is_a($abilityInterfaceName, Ability::class, TRUE)) {
            $this->getLogger()->warning(
                __METHOD__
                . ' is checking ability ' . $abilityInterfaceName
                . ' which is not sub class of ' . Ability::class
            );
            return false;
        }

        if (!$this->has($abilityInterfaceName)) {
            return false;
        }

        /**
         * @var Ability $ability
         */
        $ability = $this->make($abilityInterfaceName);
        return $ability->isAllowing($this);
    }

    /*------ id ------*/

    public function getTraceId(): string
    {
        return $this->traceId ?? $this->traceId = $this->getRequest()->fetchTraceId();
    }

    public function getConversationId(): string
    {
        return $this->getRequest()->fetchMessageId();
    }

    public function getIncomingMessage() : IncomingMessage
    {
        return $this->make(IncomingMessage::class);
    }

    public function getUser(): User
    {
        return $this->make(User::class);
    }

    public function getChat(): Chat
    {
        return $this->make(Chat::class);
    }


    /*------ components ------*/

    public function getRequest(): MessageRequest
    {
        return $this->shared[MessageRequest::class];
    }

    /**
     * @var string
     */
    protected $locale;

    protected function locale() : string
    {
        return $this->locale
            ?? $this->locale = $this->getChatbotConfig()
                    ->translation
                    ->defaultLocale;
    }

    public function reply(Message $message): void
    {
        if ($message instanceof VerboseMsg) {
            $message->translate(
                $this->make(Translator::class),
                $this->locale()
            );
        }

        $this->saveReply($message);
    }


    public function monolog(): Monologue
    {
        return $this->make(Monologue::class);
    }


    /**
     * 回复
     * @param Message $message
     */
    public function saveReply(Message $message) : void
    {
        $request = $this->getRequest();
        $incomingMessage = $this->getIncomingMessage();

        $replyMessage =  new OutgoingMessageImpl(
            $incomingMessage,
            $request->generateMessageId(),
            $message
        );

        // 先缓冲起消息来. 是不是立刻发送, request 自己决定.
        $request->bufferMessageToChat($replyMessage);

        // 这个和buffer 不一样, 用于别的处理, 比如存储消息.
        $this->replyMessages[] = $replyMessage;
    }


    /**
     * 拿到一个 conversation 的所有 reply
     * 用于给别的模块来处理.
     *
     * @return ConversationMessage[]
     */
    public function getOutgoingMessages(): array
    {
        return $this->replyMessages;
    }

    /*------ input ------*/

    public function getChatbotConfig(): ChatbotConfig
    {
        return $this->make(ChatbotConfig::class);
    }

    /*---------- signal -----------*/

    public function sendSignal(Signal $signal): void
    {
        $signal->withConversation($this);
        $this->getEventDispatcher()->listenCallable(
            ChatbotPipeStart::class,
            [$signal, 'handle']
        );

        $this->getEventDispatcher()->listenCallable(
            ChatbotPipeClose::class,
            [$signal, 'handle']
        );
    }



    /*---------- 收尾记录 -----------*/


    public function onFinish(callable $caller, bool $atEndOfTheQueue = true): void
    {
        if ($atEndOfTheQueue) {
            $this->finishCallers[] = $caller;
        } else {
            array_unshift($this->finishCallers, $caller);
        }
    }


    public function finish() : void
    {
        try {
            foreach ($this->finishCallers as $caller) {
                $this->call($caller, [Conversation::class => $this]);
            }

            $this->unsetSelf();

        } catch (\Exception $e) {
            $this->unsetSelf();

            throw new FatalErrorException($e);
        }
    }

    protected function unsetSelf()
    {
        $this->finishCallers = null;
        $this->replies = null;
        $this->replyMessages = null;
        $this->incomingMessageId = null;
        $this->asConversation = false;
        $this->flushInstance();
    }

    public function __destruct()
    {
        unset(self::$ids[$this->traceId]);
    }
}