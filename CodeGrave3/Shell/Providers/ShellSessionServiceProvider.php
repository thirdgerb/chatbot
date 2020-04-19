<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Providers;

use Commune\Container\ContainerContract;
use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellScope;
use Commune\Framework\Blueprint\Session;
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Prototype\Intercom\IGhostInput;
use Commune\Framework\Prototype\Intercom\IShellInput;
use Commune\Framework\Prototype\Intercom\IShellScope;
use Commune\Framework\Prototype\Session\ISessionLogger;
use Commune\Shell\Blueprint\Session\ShellSession;
use Commune\Shell\Blueprint\Session\ShellStorage;
use Commune\Shell\Contracts\ShellRequest;
use Commune\Shell\Prototype\Session\IShellSession;
use Commune\Shell\Prototype\Session\IShellStorage;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellSessionServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [];
    }

    public function isProcessServiceProvider(): bool
    {
        return false;
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $this->registerSession($app);
        $this->registerLogger($app);
        $this->registerStorage($app);
        $this->registerShellInput($app);
        $this->registerShellScope($app);
        $this->registerGhostInput($app);
    }

    protected function registerGhostInput(ContainerContract $app) : void
    {
        $app->singleton(
            GhostInput::class,
            function(ContainerContract $app) : GhostInput {
                /**
                 * @var ShellSession $session
                 */
                $session = $app->make(ShellSession::class);
                $request = $session->request;
                $shellInput = $session->shellInput;
                $scope = $shellInput->scope;

                return new IGhostInput(
                    $scope->shellName,
                    $scope->chatId,
                    $request->isStateless(),
                    $shellInput,
                    $request->getTraceId(),
                    $request->getSceneId(),
                    $request->getSceneEnv(),
                    $request->getMessageId(),
                    $request->getComprehension()
                );
            }
        );
    }

    protected function registerShellScope(ContainerContract $app) : void
    {
        $app->singleton(
            ShellScope::class,
            function(ContainerContract $app) : ShellScope {
                /**
                 * @var ShellSession $session
                 */
                $session = $app->make(ShellSession::class);
                $shell = $session->shell;
                $request = $session->request;

                return new IShellScope([
                    'chatbotName' => $shell->getChatbotName(),
                    'shellName' => $shell->getShellName(),
                    'chatId' => $session->getChatId(),
                    'userId' => $request->getUserId(),
                    'sessionId' => $request->getSessionId(),
                    'sceneId' => $session->getSceneId(),
                ]);
            }
        );
    }

    protected function registerShellInput(ContainerContract $app) : void
    {
        $app->singleton(
            ShellInput::class,
            function(ContainerContract $app) : ShellInput
            {

                /**
                 * @var ShellScope $scope
                 * @var ShellRequest $request
                 */
                $scope = $app->make(ShellScope::class);
                $request = $app->make(ShellRequest::class);

                return new IShellInput(
                    $request->getMessage(),
                    $scope
                );
            }
        );

    }

    protected function registerSession(ContainerContract $app) : void
    {
        $app->singleton(ShellSession::class, IShellSession::class);
    }

    protected function registerStorage(ContainerContract $app) : void
    {
        $app->singleton(
            ShellStorage::class,
            IShellStorage::class
        );
    }

    protected function registerLogger(ContainerContract $app) : void
    {
        $app->singleton(
            SessionLogger::class,
            function(ContainerContract $container) : SessionLogger {
                /**
                 * @var Session $session
                 */
                $session = $container->get(Session::class);
                $logger = $container->get(LoggerInterface::class);

                $context = $session->getRequest()->getLogContext();
                $context['serverId'] = $session->getServer()->getId();

                return new ISessionLogger(
                    $logger,
                    $context
                );
            }
        );
    }


}