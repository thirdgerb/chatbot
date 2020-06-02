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
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
use Commune\Ghost\Cloner\IClonerLogger;
use Commune\Ghost\Cloner\IClonerScene;
use Commune\Ghost\Cloner\IClonerScope;
use Commune\Ghost\Cloner\IClonerStorage;
use Commune\Ghost\Runtime\IRuntime;
use Commune\Ghost\ITools\IMatcher;
use Commune\Protocals\Intercom\InputMsg;
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
             * @var InputMsg $input
             * @var ExceptionReporter $reporter
             * @var LoggerInterface $logger
             */
            $input = $app->make(InputMsg::class);
            $logger = $app->make(LoggerInterface::class);
            $reporter = $app->make(ExceptionReporter::class);

            return new IClonerLogger(
                $logger,
                $reporter,
                [
                    'sid' => $input->getSessionId(),
                    'tid' => $input->getTraceId(),
                    'mid' => $input->getMessageId(),
                    'cid' => $input->getConversationId(),
                    'shn' => $input->getShellName(),
                ]
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