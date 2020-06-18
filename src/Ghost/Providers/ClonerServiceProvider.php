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

use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
use Commune\Ghost\Cloner\IClonerLogger;
use Commune\Ghost\Cloner\IClonerScene;
use Commune\Ghost\Cloner\IClonerScope;
use Commune\Ghost\Cloner\IClonerStorage;
use Commune\Ghost\Runtime\IRuntime;
use Commune\Ghost\ITools\IMatcher;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ClonerServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }

    public function boot(Container $app): void
    {
    }

    public function register(Container $app): void
    {
        $this->registerCloneScope($app);
        $this->registerCloneLogger($app);
        $this->registerCloneScene($app);
        $this->registerCloneMatcher($app);
        $this->registerAuth($app);
        $this->registerRuntime($app);
        $this->registerStorage($app);
    }


    /*-------- register --------*/

    protected function registerCloneMatcher(Container $app) : void
    {
        $app->bind(Matcher::class, function(Container $app) {
            $cloner = $app->get(Cloner::class);
            return new IMatcher($cloner, []);
        });
    }

    /**
     * 场景信息.
     * @param Container $app
     */
    protected function registerCloneScene(Container $app) : void
    {
        $app->singleton(ClonerScene::class, IClonerScene::class);
    }

    protected function registerCloneScope(Container $app) : void
    {
        $app->singleton(ClonerScope::class, IClonerScope::class);
    }

    /**
     * 对话日志.
     * @param Container $app
     */
    protected function registerCloneLogger(Container $app) : void
    {
        $app->singleton(ClonerLogger::class, function(Container $app) {
            /**
             * @var LoggerInterface $logger
             */
            $logger = $app->make(LoggerInterface::class);
            return new IClonerLogger(
                $logger,
                $app
            );
        });
    }

    protected function registerAuth(Container $app) : void
    {
        $app->singleton(Authority::class, IAuthority::class);
    }

    protected function registerRuntime(Container $app) : void
    {
        $app->singleton(Runtime::class, IRuntime::class);
    }

    protected function registerStorage(Container $app) : void
    {
        $app->singleton(ClonerStorage::class, IClonerStorage::class);
    }

}