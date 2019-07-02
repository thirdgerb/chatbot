<?php


namespace Commune\Chatbot\App\Platform\ReactorStdio;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Drivers\Demo\SimpleExpHandler;
use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Contracts\ExceptionHandler;

class RSServerProvider extends ServiceProvider
{
    public function boot($app): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(ChatServer::class, StdioServer::class);

        $this->app->singleton(Supervise::class, function(){

            // 用匿名类简单实现一个ability
            return new class implements Supervise {

                public function isAllowing(Conversation $conversation): bool
                {
                    /**
                     * @var ConsoleConfig $config
                     */
                    $config = $conversation[ConsoleConfig::class];
                    return $conversation->getUser()->getId() === $config->consoleUserId;
                }
            };
        });

        $this->app->singleton(
            ExceptionHandler::class,
            SimpleExpHandler::class
        );
    }


}