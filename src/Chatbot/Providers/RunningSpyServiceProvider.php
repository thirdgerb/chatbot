<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Providers;

use Commune\Chatbot\Blueprint\Chatbot;
use Commune\Chatbot\ChatbotConfig;
use Commune\Container\ContainerContract;
use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Support\RunningSpy\SpyAgency;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RunningSpyServiceProvider extends ServiceProvider
{
    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var ChatbotConfig $config
         */
        $config = $app->get(ChatbotConfig::class);
        SpyAgency::$running = $config->debug;
    }

    public function register(ContainerContract $app): void
    {
    }

    public static function stub(): array
    {
        return [];
    }


}