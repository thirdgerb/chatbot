<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Ghost\Blueprint\Auth\Authority;
use Commune\Ghost\Blueprint\Session\GhtSession;
use Commune\Ghost\Blueprint\Session\Scene;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Auth\IAuthority;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostReqServiceProvider extends ServiceProvider
{
    public function isProcessServiceProvider(): bool
    {
        return false;
    }

    public function boot(ContainerContract $app): void
    {
    }


    public static function stub(): array
    {
        return [];
    }

    public function register(ContainerContract $app): void
    {
        $this->registerAuth($app);
        $this->registerScene($app);
    }

    protected function registerAuth(ContainerContract $app): void
    {
        $app->singleton(Authority::class, IAuthority::class);
    }

    protected function registerScene(ContainerContract $app) : void
    {
        $app->singleton(Scene::class, function(ContainerContract $app){

            /**
             * @var GhtSession $session
             * @var GhostConfig $config
             */
            $session = $app->make(GhtSession::class);
            $config = $app->make(GhostConfig::class);
            $ghostInput = $session->ghostInput;

            $scene = $config->getScene($ghostInput->sceneId, $ghostInput->sceneEnv);

            return $scene;
        });
    }


}