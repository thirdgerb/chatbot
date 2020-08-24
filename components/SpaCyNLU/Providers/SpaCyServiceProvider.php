<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Providers;

use Commune\Components\SpaCyNLU\Blueprint\SpaCyNLUClient;
use Commune\Components\SpaCyNLU\Impl\GuzzleSpaCyNLUClient;
use Commune\Components\SpaCyNLU\NLU\SpaCyNLUService;
use Commune\Components\SpaCyNLU\NLU\SpaCySimpleChat;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $clientImpl
 */
class SpaCyServiceProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }

    public static function stub(): array
    {
        return [
            'clientImpl' => GuzzleSpaCyNLUClient::class,
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(SpaCyNLUClient::class, $this->clientImpl);

        $app->singleton(SpaCyNLUService::class);
        $app->singleton(SpaCySimpleChat::class);
    }


}