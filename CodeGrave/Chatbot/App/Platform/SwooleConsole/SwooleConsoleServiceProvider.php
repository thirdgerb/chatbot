<?php


namespace Commune\Chatbot\App\Platform\SwooleConsole;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\ServiceProvider;

class SwooleConsoleServiceProvider extends ServiceProvider
{
    public function boot($app): void
    {
    }

    public function register(): void
    {
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

    }


}