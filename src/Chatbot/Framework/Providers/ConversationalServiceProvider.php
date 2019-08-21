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
use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Framework\Conversation\ChatImpl;
use Commune\Chatbot\Framework\Conversation\ConversationLoggerImpl;
use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use Commune\Chatbot\Framework\Conversation\MonologueImpl;
use Commune\Chatbot\Framework\Conversation\UserImpl;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Listeners\HearingHandler;
use Commune\Chatbot\OOHost\Session\Driver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

class ConversationalServiceProvider extends BaseServiceProvider
{
    public function boot($app): void
    {
    }


    public function register(): void
    {
        $this->registerDefaultSlots();
        $this->registerMonologue();
        $this->registerIncomingMessage();
        $this->registerUser();
        $this->registerChat();
        $this->registerLogger();
        $this->registerHearing();
        $this->registerSession();
    }

    protected function registerDefaultSlots() : void
    {
        $this->app->singleton(
            Monologue::DEFAULT_SLOTS,
            function(Conversation $conversation){

                $env = $conversation->getChatbotConfig()->slots;
                $slots = Arr::dot($env);

                /**
                 * @var User $user
                 */
                $user = $conversation[User::class];
                $slots[Monologue::SLOT_USER_NAME] = $user->getName();

                return $slots;
            }
        );
    }

    protected function registerSession() : void
    {
        $this->app->bind(Session::class, function($conversation, $parameters){
            return new SessionImpl(
                $parameters['belongsTo'],
                $parameters['cache'],
                $conversation,
                $conversation[Driver::class]
            );
        });
    }

    protected function registerHearing() : void
    {
        // 可以重写成自己觉得合适的
        $this->app->bind(
            Hearing::class,
            function($app, $parameters){
                return new HearingHandler(
                    $parameters['context'],
                    $parameters['dialog'],
                    $parameters['message']
                );
            }
        );
    }

    protected function registerUser() : void
    {
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
        $this->app->singleton(
            Chat::class,
            function(Conversation $app) {
                $request = $app->getRequest();
                $chat = new ChatImpl(
                    $request->getPlatformId(),
                    $request->fetchUserId(),
                    $request->getChatbotUserId()
                );
                return $chat;
            }
        );


    }

    protected function registerMonologue() : void
    {
        $this->app->singleton(
            Monologue::class,
            MonologueImpl::class
        );
    }

    protected function registerIncomingMessage()
    {
        $this->app->singleton(
            IncomingMessage::class,
            function(Conversation $app) {
                $request = $app->getRequest();
                $chat = $app->getChat();
                $incomingMessage = new IncomingMessageImpl(
                    $request->fetchMessageId(),
                    $request->fetchMessage(),
                    $chat->getUserId(),
                    $chat->getChatbotUserId(),
                    $chat->getPlatformId(),
                    $chat->getChatId(),
                    null,
                    $request->fetchTraceId()
                );
                return $incomingMessage;
            }
        );

    }

    protected function registerLogger() : void
    {
        $this->app->singleton(ConversationLogger::class, function($app){
            return new ConversationLoggerImpl(
                $app[LoggerInterface::class],
                $app
            );
        });

    }
}