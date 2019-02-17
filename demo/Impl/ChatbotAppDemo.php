<?php

/**
 * Class Application
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Command\AnalyzerPipe;
use Commune\Chatbot\Command\Commands;
use Commune\Chatbot\Command\UserCommandPipe;
use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Contracts\ServerDriver;
use Commune\Chatbot\Framework\Bootstrap\PreloadContextConfig;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Chat\ChatPipe;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\HostPipe;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Demo\Configure\ContextCfg;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Container\Container;

class ChatbotAppDemo implements ChatbotApp
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var IntentRoute
     */
    protected $defIntentRoute;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->config = $this->getChatbotConfig();
    }

    public function getChatbotConfig(): array
    {

        return [
            'runtime' => [
                'direct_max_ticks' => 30,
                'bootstrappers' => [
                    PreloadContextConfig::class,
                ],
                'pipes' => [
                    ChatPipe::class,
                    AnalyzerPipe::class,
                    UserCommandPipe::class,
                    HostPipe::class
                ],
                'analyzers' => [
                    Commands\Locate::class,
                    Commands\ShowContext::class,
                    Commands\History::class,
                    Commands\Scoping::class,
                ],
                'analyzer_mark' => '/',
                'commands' => [
                    Commands\Quit::class,
                    Commands\WhoAmI::class,
                    Commands\Where::class,
                    Commands\Backward::class,
                    Commands\Forward::class,
                    Commands\Cancel::class,
                    Commands\Repeat::class,
                ],
                'command_mark' => '.',
            ],


            'contexts' => [
                'root' => ContextCfg\Root::class,
                'preload' => [
                    ContextCfg\Root::class,
                    ContextCfg\Test::class
                ]
            ],

            'messages' => [
                'miss_match_message' => 'miss match',
                'exceptions' => [
                    0 => 'unexpected exception occur',
                ],
                'ask_intent_argument' => '请输入{key}({desc}):'
            ],
        ];
    }

    public function isSupervisor(User $sender): bool
    {
        return true;
    }

    public function getContainer(): Container
    {
        return $this->app;
    }


    public function getServerDriver(): ServerDriver
    {
        $driver = $this->make(ServerDriver::class);
        return $driver;
    }

    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->make(ExceptionHandler::class);
    }


    public function getIntentDefaultRoute(Router $router): IntentRoute
    {
        if (!isset($this->defIntentRoute)) {
            $this->defIntentRoute = new IntentRoute($this, $router);
            $this->defIntentRoute
                ->call(function (Context $context, Intent $intent) {
                    $context->error('miss match intent : ' . $intent);
                })
                ->home();

        }

        return $this->defIntentRoute;
    }

    public function make($abstract, array $parameters = [])
    {
        return $this->app->make($abstract, $parameters);
    }

    public function get($id)
    {
        return $this->app->get($id);
    }

    public function has($id)
    {
        return $this->app->has($id);
    }

    public function getConfig(string $configConstantName, $default = null)
    {
        return Arr::get($this->config, $configConstantName, $default);
    }


}