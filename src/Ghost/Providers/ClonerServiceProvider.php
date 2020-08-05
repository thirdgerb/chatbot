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
use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session\SessionScene;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Ghost\Auth\ISupervise;
use Commune\Ghost\Cloner as ICloner;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
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
        $this->registerAvatar($app);
        $this->registerDispatcher($app);
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
        $app->singleton(
            Cloner\ClonerScene::class,
            function(Container $app) {
                return ICloner\IClonerScene::factory($app);
            }
        );
    }

    protected function registerCloneScope(Container $app) : void
    {
        $app->singleton(
            Cloner\ClonerScope::class,
            function(ReqContainer $app) {
                return ICloner\IClonerScope::factory($app);
            }
        );

        $app->singleton(
            SessionScene::class,
            Cloner\ClonerScene::class
        );
    }

    /**
     * 对话日志.
     * @param Container $app
     */
    protected function registerCloneLogger(Container $app) : void
    {
        $app->singleton(
            Cloner\ClonerLogger::class,
            function(Container $app) {
                /**
                 * @var LoggerInterface $logger
                 */
                $logger = $app->make(LoggerInterface::class);
                return new ICloner\IClonerLogger(
                    $logger,
                    $app
                );
            }
        );
    }

    protected function registerAuth(Container $app) : void
    {
        $app->singleton(Authority::class, IAuthority::class);
        $app->singleton(Supervise::class, ISupervise::class);
    }

    protected function registerRuntime(Container $app) : void
    {
        $app->singleton(Runtime::class, IRuntime::class);
    }

    protected function registerStorage(Container $app) : void
    {
        $app->singleton(
        Cloner\ClonerStorage::class,
        ICloner\IClonerStorage::class
        );
    }

    protected function registerAvatar(Container $app) : void
    {
        $app->singleton(
            Cloner\ClonerAvatar::class,
            ICloner\IClonerAvatar::class
        );
    }

    protected function registerDispatcher(Container $app) : void
    {
        $app->singleton(
            Cloner\ClonerDispatcher::class,
            ICloner\IClonerDispatcher::class
        );

    }
}