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
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Replies\Paragraph;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Blueprint\Message\Tags\SelfTranslating;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Container\ContainerContract;
use Commune\Container\RecursiveContainer;

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
    use RecursiveContainer, RunningSpyTrait;

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
     * @var bool
     */
    protected $asked = false;

    /**
     * @var ConversationMessage[]
     */
    protected $replyMessages = [];

    /**
     * @var ConversationMessage[]
     */
    protected $bufferMessages = [];

    /**
     * @var bool
     */
    protected $asConversation = false;

    /**
     * @var \Closure[]
     */
    protected $finishCallers = [];

    /**
     * @var NLU
     */
    protected $nlu;

    public function onMessage(MessageRequest $request, ChatbotConfig $config): Blueprint
    {
        $container = new static($this->getProcessContainer());
        // container is instance for request
        $container->asConversation = true;

        // 绑定自身.
        $container->share(Conversation::class, $container);

        // share request

        // 互相持有, 要注意内存泄露的问题.
        $container->share( MessageRequest::class, $request);
        $container->share(get_class($request), $request);
        $request->withConversation($container);

        $trace = $container->getTraceId();
        static::addRunningTrace($trace, $container->getConversationId());
        return $container;
    }

    public function getProcessContainer(): ContainerContract
    {
        return $this->getParentContainer();
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->make(EventDispatcher::class);
    }

    public function fire(object $event): void
    {
        $this->getEventDispatcher()->dispatch($event, $this);
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

    public function getNLU(): NLU
    {
        return $this->nlu
            ?? $this->nlu = (
                $this->getRequest()->fetchNLU()
                ?? new NatureLanguageUnit()
            );
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

    public function reply(Message $message, bool $immediately = false): void
    {
        if ($message instanceof Question) {
            $this->asked = true;
        }

        $messages = $this->render($message);

        foreach ($messages as $msg) {
            $request = $this->getRequest();
            $incomingMessage = $this->getIncomingMessage();

            $replyMessage =  new OutgoingMessageImpl(
                $incomingMessage,
                $request->generateMessageId(),
                $msg
            );

            $this->saveConversationReply($request, $replyMessage, $immediately);
        }
    }

    public function hasAsked(): bool
    {
        return $this->asked;
    }


    protected function defaultSlots() : array
    {
        return $this[Speech::DEFAULT_SLOTS];
    }

    /**
     * @param Message $message
     * @return array
     * @throws
     */
    public function render(Message $message): array
    {
        // only reply message should be rendered
        if (!$message instanceof ReplyMsg) {
            return [$message];
        }

        // only way to merge default slots
        $message->mergeSlots($this->defaultSlots());

        // selfTranslate test
        if ($message instanceof SelfTranslating) {
            $message->translateBy($this->make(Translator::class));
        }

        // paragraph
        if ($message instanceof Paragraph) {
            $rendered = [];
            foreach ($message->getReplies() as $reply) {
                $rendered = array_merge($rendered, $this->render($reply));
            }
            $texts = array_map(
                function(Message $message){ return $message->getText(); },
                $rendered
            );
            $message->withText(...$texts);
            return [$message];
        }


        /**
         * @var Renderer $renderer
         */
        $renderer = $this->get(Renderer::class);
        $id = $message->getReplyId();

        // use template to render message
        if ($renderer->boundTemplate($id)) {
            return $renderer
                ->makeTemplate($id)
                ->render($message, $this);
        }

        // default renderer do translate only
        return $renderer
            ->makeTemplate(Renderer::DEFAULT_ID)
            ->render($message, $this);
    }


    public function deliver(
        string $userId,
        Message $message,
        bool $immediately = false,
        string $chatId = null
    ): void
    {

        $messages = $this->render($message);


        foreach ($messages as $msg) {
            $request = $this->getRequest();
            $chat = new ChatImpl(
                $this->make(CacheAdapter::class),
                $request->getPlatformId(),
                $userId,
                $request->getChatbotName(),
                $chatId
            );

            $toChat = new ToChatMessage(
                $chat,
                $request->generateMessageId(),
                $msg,
                $this->getTraceId()
            );
            $this->saveConversationReply($request, $toChat,  $immediately);
        }
    }


    public function getSpeech(): Speech
    {
        return $this->make(Speech::class);
    }


    public function saveConversationReply(
        MessageRequest $request,
        ConversationMessage $message,
        bool  $immediatelyBuffer
    ) : void
    {
        if ($immediatelyBuffer) {
            // 先缓冲起消息来. 是不是立刻发送, request 自己决定.
            $request->bufferMessage($message);
        } else {
            // 这个和buffer 不一样, 用于别的处理, 比如存储消息.
            $this->bufferMessages[] = $message;
        }

        $this->replyMessages[] = $message;
    }

    public function flushConversationReplies(): void
    {
        $this->replyMessages = [];
        $this->bufferMessages = [];
    }

    public function getReplies(): array
    {
        return $this->replyMessages;
    }

    public function countReplies(): int
    {
        return count($this->replyMessages);
    }


    /*------ input ------*/

    public function getChatbotConfig(): ChatbotConfig
    {
        return $this->make(ChatbotConfig::class);
    }

    /*---------- signal -----------*/
//
//    /**
//     * @deprecated
//     * @param Signal $signal
//     */
//    public function sendSignal(Signal $signal): void
//    {
//        $signal->withConversation($this);
//        $this->getEventDispatcher()->listenCallable(
//            ChatbotPipeStart::class,
//            [$signal, 'handle']
//        );
//
//        $this->getEventDispatcher()->listenCallable(
//            ChatbotPipeClose::class,
//            [$signal, 'handle']
//        );
//    }




    /*---------- 收尾记录 -----------*/


    public function onFinish(callable $caller, bool $atEndOfTheQueue = true): void
    {
        if ($atEndOfTheQueue) {
            $this->finishCallers[] = $caller;
        } else {
            array_unshift($this->finishCallers, $caller);
        }
    }

    public function finishRequest(): void
    {
        try {

            // 发送消息.
            $request = $this->getRequest();

            foreach ($this->bufferMessages as $message) {
                $request->bufferMessage($message);
            }

            $this->flushConversationReplies();

            // 发送所有消息.
            $request->sendResponse();

        } catch (\Exception $e) {
            throw new RuntimeException($e);
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
            throw new RuntimeException($e);
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
        static::removeRunningTrace($this->traceId);
    }
}