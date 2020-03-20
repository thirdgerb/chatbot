<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\ChatAppConfig;
use Commune\Framework\Blueprint\Container;
use Commune\Ghost\Blueprint\GhostConfig;
use Commune\Platform\Server;
use Commune\Shell\Blueprint\Kernel\ApiKernel;
use Commune\Shell\Blueprint\Kernel\CallbackKernel;
use Commune\Shell\Blueprint\Kernel\UserKernel;
use Commune\Shell\Blueprint\Shell as Contract;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Blueprint\ShellConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellImpl implements Contract
{
    /**
     * @var ChatApp
     */
    protected $app;

    /**
     * @var ShellConfig
     */
    protected $shellConfig;

    /**
     * @var Container
     */
    protected $pc;

    /**
     * @var Container
     */
    protected $cc;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Server
     */
    protected $server;

    public function __construct(ChatApp $app, ShellConfig $config)
    {
        $this->shellConfig = $config;
        $this->name = $config->name;
        $this->pc = $this->app->getProcessContainer();
        $this->cc = $this->app->getConversationContainer();
        $this->basicBindings();
    }

    protected function basicBindings() : void
    {
        // basic bindings
        $instances = [
            ChatApp::class => $this->app,
            Shell::class => $this,
            ChatAppConfig::class => $config = $this->app->getConfig(),
            ShellConfig::class => $this->shellConfig,
            GhostConfig::class => $config->ghost,
            UserKernel::class => $config->userKernel,
            ApiKernel::class => $config->apiKernel,
            CallbackKernel::class => $config->callbackKernel,
        ];

        foreach ($instances as $abs => $singleton) {
            $this->bindInstance($abs, $singleton);
        }
    }

    public function bindInstance(string $abstract, $singleton) : void
    {
        $this->pc->instance($abstract, $singleton);
        $this->cc->instance($abstract, $singleton);
    }


    public function name(): string
    {
        return $this->name;
    }

    public function getProcessContainer(): Container
    {
        return $this->pc;
    }

    public function getConversationContainer(): Container
    {
        return $this->cc;
    }

    public function getAppConfig(): ChatAppConfig
    {
        return $this->app->getConfig();
    }

    public function getShellConfig(): ShellConfig
    {
        return $this->shellConfig;
    }

    /*------- boot -------*/

    protected $booted = false;

    public function boot(Server $server): void
    {
        $this->server = $server;
        $this->bindInstance(Server::class, $server);


    }

    public function get($id)
    {
        return $this->pc->get($id);
    }

    public function has($id)
    {
        return $this->pc->has($id);
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getApiKernel(): ApiKernel
    {
        // TODO: Implement getApiKernel() method.
    }

    public function getUserKernel(): UserKernel
    {
        // TODO: Implement getUserKernel() method.
    }

    public function getCallbackKernel(): CallbackKernel
    {
        // TODO: Implement getCallbackKernel() method.
    }


}