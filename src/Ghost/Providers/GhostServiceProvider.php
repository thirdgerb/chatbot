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
use Commune\Blueprint\Ghost\ClonerScope;
use Commune\Blueprint\Ghost\Convo\ConvoLogger;
use Commune\Blueprint\Ghost\Convo\ConvoScene;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
use Commune\Ghost\Convo\IConvoLogger;
use Commune\Ghost\Convo\IConvoScene;
use Commune\Ghost\IClonerScope;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostServiceProvider extends ServiceProvider
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
        $this->registerConvo($app);
        $this->registerConvoScope($app);
        $this->registerConvoLogger($app);
        $this->registerConvoScene($app);

        $this->registerAuth($app);
        $this->registerMindset($app);
        $this->registerRuntime($app);

        $this->registerStorage($app);
    }


    /*-------- register --------*/


    /**
     * 场景信息.
     * @param Container $app
     */
    protected function registerConvoScene(Container $app) : void
    {
        $app->singleton(ConvoScene::class, IConvoScene::class);
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
        $app->singleton(ConvoLogger::class, function(Container $app) {
            /**
             * @var ClonerScope $scope
             * @var ExceptionReporter $reporter
             * @var LoggerInterface $logger
             */
            $scope = $app->make(ClonerScope::class);
            $logger = $app->make(LoggerInterface::class);
            $reporter = $app->make(ExceptionReporter::class);

            return new IConvoLogger($logger, $reporter, $scope->toArray());
        });
    }

    protected function registerAuth(Container $app) : void
    {
        $app->singleton(Authority::class, IAuthority::class);
    }

}