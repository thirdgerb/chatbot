<?php

namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Contracts\ClientFactory;
use Commune\Chatbot\Framework\Conversation\ChatImpl;
use Commune\Chatbot\Framework\Conversation\ConversationLoggerImpl;
use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use Commune\Chatbot\Framework\Conversation\NatureLanguageUnit;
use Commune\Chatbot\Framework\Conversation\SpeechImpl;
use Commune\Chatbot\Framework\Conversation\UserImpl;
use Commune\Chatbot\Framework\Predefined\GuzzleClientFactory;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

/**
 * 请求级服务和单例的注册类.
 */
class ConversationalServiceProvider extends BaseServiceProvider
{
    public function boot($app): void
    {
    }


    public function register(): void
    {
        $this->registerSpeech();
        $this->registerIncomingMessage();
        $this->registerUser();
        $this->registerChat();
        $this->registerLogger();
        $this->registerClientFactory();
        $this->registerDefaultSlots();
        $this->registerNLU();
    }

    /**
     * 注册 user 模块.
     */
    protected function registerUser() : void
    {
        if ($this->app->bound(User::class)) {
            return;
        }

        $this->app->singleton(
            User::class,
            function(Conversation $app) {
                $request = $app->getRequest();
                $chat = $app->getChat();
                $user = new UserImpl(
                    $chat->getUserId(),
                    $request->fetchUserName(),
                    $request->fetchUserData()
                );
                return $user;
            }
        );

    }

    /**
     * 注册 Chat 模块
     */
    protected function registerChat() : void
    {
        if ($this->app->bound(Chat::class)) {
            return;
        }

        $this->app->singleton(
            Chat::class,
            function(Conversation $app) {
                $request = $app->getRequest();
                $chat = new ChatImpl(
                    $app[CacheAdapter::class],
                    $request->getPlatformId(),
                    $request->fetchUserId(),
                    $request->getChatbotName(),
                    // chatId could be null
                    $request->fetchChatId()
                );
                return $chat;
            }
        );


    }

    /**
     * 注册 Speech 模块
     */
    protected function registerSpeech() : void
    {
        if ($this->app->bound(Speech::class)) {
            return;
        }
        $this->app->singleton(
            Speech::class,
            SpeechImpl::class
        );
    }

    /**
     * 注册 IncomingMessage 模块.
     */
    protected function registerIncomingMessage()
    {
        if ($this->app->bound(IncomingMessage::class)) {
            return;
        }

        $this->app->singleton(
            IncomingMessage::class,
            function(Conversation $app) {
                $request = $app->getRequest();
                $chat = $app->getChat();
                $incomingMessage = new IncomingMessageImpl(
                    $request->fetchMessageId(),
                    $request->fetchMessage(),
                    $chat->getUserId(),
                    $chat->getChatbotName(),
                    $chat->getPlatformId(),
                    $chat->getChatId(),
                    null,
                    $request->fetchSessionId(),
                    $request->fetchTraceId()
                );
                return $incomingMessage;
            }
        );
    }

    /**
     * 注册日志模块.
     */
    protected function registerLogger() : void
    {
        // 如果已经绑定, 就跳过
        if ($this->app->bound(ConversationLogger::class)) {
            return;
        }

        $this->app->singleton(ConversationLogger::class, function($app){
            return new ConversationLoggerImpl(
                $app[LoggerInterface::class],
                $app[Conversation::class]
            );
        });


    }

    protected function registerClientFactory() : void
    {
        if ($this->app->bound(ClientFactory::class)) {
            return;
        }

        $this->app->singleton(ClientFactory::class, function($app){
            return new GuzzleClientFactory();
        });
    }


    /**
     * 注册默认slots
     */
    protected function registerDefaultSlots() : void
    {
        $this->app->singleton(
            Speech::DEFAULT_SLOTS,
            function(Conversation $conversation){

                $config = $conversation->getChatbotConfig();
                $env = $config->defaultSlots;
                $slots = Arr::dot($env);

                foreach ($slots as $key => $value) {
                    $slots[str_replace('.', '_', $key)] = $value;
                }

                /**
                 * @var User $user
                 */
                $user = $conversation[User::class];

                // 用户名
                $slots[Speech::SLOT_USER_NAME] = $user->getName();
                $slots[Speech::SLOT_CHATBOT_NAME] = $config->chatbotName;

                return $slots;
            }
        );
    }

    protected function registerNLU() : void
    {
        if ($this->app->bound(NLU::class)) {
            return;
        }

        $this->app->singleton(NLU::class, function($app){
            /**
             * @var MessageRequest $request
             */
            $request = $app[MessageRequest::class];
            return $request->fetchNLU() ?? new NatureLanguageUnit();
        });

    }
}