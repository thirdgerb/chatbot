<?php


namespace Commune\Chatbot\App\Platform\ReactorStdio;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\ServiceProvider;

class RSServerProvider extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

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