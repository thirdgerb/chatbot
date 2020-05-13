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

use Commune\Blueprint\Ghost\Auth\Authority;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
use Commune\Ghost\Cloner\IClonerLogger;
use Commune\Ghost\Cloner\IClonerScene;
use Commune\Ghost\Cloner\IClonerScope;
use Commune\Ghost\Cloner\IClonerStorage;
use Commune\Ghost\Routing\IMatcher;
use Commune\Ghost\Runtime\IRuntime;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostReqServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [];
    }


    public function boot(Container $app): void
    {
    }

    public function register(Container $app): void
    {
        $this->registerConvoScope($app);
        $this->registerConvoLogger($app);
        $this->registerConvoScene($app);
        $this->registerAuth($app);
        $this->registerRuntime($app);
        $this->registerMatcher($app);
        $this->registerStorage($app);
    }


    /*-------- register --------*/


    /**
     * 场景信息.
     * @param Container $app
     */
    protected function registerConvoScene(Container $app) : void
    {
        $app->singleton(ClonerScene::class, IClonerScene::class);
    }

    protected function registerConvoScope(Container $app) : void
    {
        $app->singleton(ClonerScope::class, IClonerScope::class);
    }

    /**
     * 对话日志.
     * @param Container $app
     */
    protected function registerConvoLogger(Container $app) : void
    {
        $app->singleton(ClonerLogger::class, function(Container $app) {
            /**
             * @var ClonerScope $scope
             * @var ExceptionReporter $reporter
             * @var LoggerInterface $logger
             */
            $scope = $app->make(ClonerScope::class);
            $logger = $app->make(LoggerInterface::class);
            $reporter = $app->make(ExceptionReporter::class);

            return new IClonerLogger($logger, $reporter, $scope->toArray());
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

    protected function registerMatcher(Container $app) : void
    {
        $app->singleton(Matcher::class, IMatcher::class);
    }
}