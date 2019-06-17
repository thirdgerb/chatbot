<?php

/**
 * Class ConversationalServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Chatbot\Framework\Conversation\ChatImpl;
use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use Commune\Chatbot\Framework\Conversation\MonologueImpl;
use Commune\Chatbot\Framework\Conversation\UserImpl;

class ConversationalServiceProvider extends BaseServiceProvider
{
    public function boot($app): void
    {
    }


    public function register(): void
    {
        $this->registerMonologue();
        $this->registerIncomingMessage();
        $this->registerUser();
        $this->registerChat();
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
                    null,
                    $request->fetchTraceId()
                );
                return $incomingMessage;
            }
        );

    }
}