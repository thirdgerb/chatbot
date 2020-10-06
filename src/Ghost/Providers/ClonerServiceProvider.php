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
 * 注册 Ghost Cloner 的相关组件.
 * 开发者可以注册自己的组件以覆盖它们.
 *
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
        $this->registerCloneEnv($app);
        $this->registerCloneGuest($app);
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
        if ($app->has(Matcher::class)) {
            return;
        }

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
        if ($app->has(Cloner\ClonerScene::class)) {
            return;
        }

        $app->singleton(
            Cloner\ClonerScene::class,
            function(Container $app) {
                return ICloner\IClonerScene::factory($app);
            }
        );
    }

    /**
     * 场景信息.
     * @param Container $app
     */
    protected function registerCloneGuest(Container $app) : void
    {
        if ($app->has(Cloner\ClonerGuest::class)) {
            return;
        }

        $app->singleton(
            Cloner\ClonerGuest::class,
            ICloner\IClonerGuest::class
        );
    }

    /**
     * 场景信息.
     * @param Container $app
     */
    protected function registerCloneEnv(Container $app) : void
    {
        if ($app->has(Cloner\ClonerEnv::class)) {
            return;
        }

        $app->singleton(
            Cloner\ClonerEnv::class,
            function(Container $app) {
                return ICloner\IClonerEnv::factory($app);
            }
        );
    }

    protected function registerCloneScope(Container $app) : void
    {
        if ($app->has(Cloner\ClonerScope::class)) {
            return;
        }
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
        if ($app->has(Cloner\ClonerLogger::class)) {
            return;
        }
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
        if (!$app->has(Authority::class)) {
            $app->singleton(Authority::class, IAuthority::class);
        }
        if (!$app->has(Supervise::class)) {
            $app->singleton(Supervise::class, ISupervise::class);
        }
    }

    protected function registerRuntime(Container $app) : void
    {
        if ($app->has(Runtime::class)) {
            return;
        }
        $app->singleton(Runtime::class, IRuntime::class);
    }

    protected function registerStorage(Container $app) : void
    {
        if ($app->has(Cloner\ClonerStorage::class)) {
            return;
        }
        $app->singleton(
        Cloner\ClonerStorage::class,
        ICloner\IClonerStorage::class
        );
    }

    protected function registerAvatar(Container $app) : void
    {
        if ($app->has(Cloner\ClonerAvatar::class)) {
            return;
        }
        $app->singleton(
            Cloner\ClonerAvatar::class,
            ICloner\IClonerAvatar::class
        );
    }

    protected function registerDispatcher(Container $app) : void
    {
        if ($app->has(Cloner\ClonerDispatcher::class)) {
            return;
        }

        $app->singleton(
            Cloner\ClonerDispatcher::class,
            ICloner\IClonerDispatcher::class
        );
    }
}