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

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Ghost\Cloner\ClonerScene;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Container\ContainerContract as Container;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\Auth\IAuthority;
use Commune\Ghost\Cloner\IClonerLogger;
use Commune\Ghost\Cloner\IClonerScene;
use Commune\Ghost\Cloner\IClonerScope;
use Commune\Ghost\Cloner\IClonerStorage;
use Commune\Ghost\Runtime\IRuntime;
use Commune\Ghost\ITools\IMatcher;
use Commune\Message\Intercom\IInputMsg;
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
        $app->singleton(ClonerScene::class, function(Container $app) {

            $env = [];
            $root = '';
            if ($app->bound(GhostRequest::class)) {
                /**
                 * @var GhostRequest $request
                 */
                $request = $app->make(GhostRequest::class);
                $env = $request->getEnv();
                $root = $request->getEntry();
            }

            /**
             * @var GhostConfig $config
             */
            $config = $app->make(GhostConfig::class);
            $scenes = $config->sceneContextNames;

            $entry = Ucl::decode($root);
            $isValid = $entry->isValidPattern()
                && in_array($entry->contextName, $scenes);

            $entry = $isValid
                ? $entry
                : Ucl::decode($config->defaultContextName);

            return new IClonerScene($entry, $env);
        });
    }

    protected function registerCloneScope(Container $app) : void
    {
        $app->singleton(ClonerScope::class, function(Container $app) {

            $data = [];
            if ($app->bound(GhostRequest::class)) {
                /**
                 * @var GhostRequest $request
                 */
                $request = $app->make(GhostRequest::class);
                $data[ClonerScope::SHELL_ID] = $request->getFromApp();
            }

            if ($app->bound(InputMsg::class)) {
                /**
                 * @var InputMsg $input
                 */
                $input = $app->make(InputMsg::class);
                $data[ClonerScope::GUEST_ID] = $input->getCreatorId();
            }

            if ($app->bound(Cloner::class)) {
                /**
                 * @var Cloner $cloner
                 */
                $cloner = $app->make(Cloner::class);
                $data[ClonerScope::CONVO_ID] = $cloner->getConversationId();
                $data[ClonerScope::SESSION_ID] = $cloner->getSessionId();
            }


            return new IClonerScope($data);
        });
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