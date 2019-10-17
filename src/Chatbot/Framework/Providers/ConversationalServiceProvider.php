<?php

/**
 * Class ConversationalServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Contracts\ClientFactory;
use Commune\Chatbot\Framework\Conversation\ChatImpl;
use Commune\Chatbot\Framework\Conversation\ConversationLoggerImpl;
use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use Commune\Chatbot\Framework\Conversation\SpeechImpl;
use Commune\Chatbot\Framework\Conversation\UserImpl;
use Commune\Chatbot\Framework\Predefined\GuzzleClientFactory;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

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
    }


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

}