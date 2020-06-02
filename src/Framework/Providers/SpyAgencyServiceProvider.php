<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\App;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\RunningSpy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SpyAgencyServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
        $isDebug = CommuneEnv::isDebug();
        SpyAgency::$running = $isDebug;
    }

    public function register(ContainerContract $app): void
    {
    }


}