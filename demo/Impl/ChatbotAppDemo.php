<?php

/**
 * Class Application
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Analyzer\AnalyzerPipe;
use Commune\Chatbot\Analyzer\Commands;
use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Contracts\ServerDriver;
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
                ],
                'pipes' => [
                    ChatPipe::class,
                    AnalyzerPipe::class,
                    HostPipe::class
                ],
                'analyzers' => [
                    Commands\Location::class,
                    Commands\WhoAmI::class,
                    Commands\Where::class,
                    Commands\Quit::class,
                    Commands\History::class,
                    Commands\Scope::class,
                ],
            ],


            'contexts' => [
                'root' => ContextCfg\Root::class,
                'classes' => [
                    ContextCfg\Root::class,
                    ContextCfg\Test::class
                ]
            ],

            'messages' => [
                'miss_match_message' => 'miss match',
            ],
        ];
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

    public function getMissMatchMessage(): string
    {
        return Arr::get($this->config, 'messages.miss_match_message');
    }

    public function getRootContext(): string
    {
        return Arr::get($this->config, 'contexts.root');
    }

    public function getDirectorMaxTicks(): int
    {
        return Arr::get($this->config, 'runtime.direct_max_ticks', 10);
    }

    public function getRuntimePipes(): array
    {
        return Arr::get($this->config, 'runtime.pipes');
    }

    public function getBootstrappers(): array
    {
        return Arr::get($this->config, 'runtime.bootstrappers');
    }

    public function getContextConfigs(): array
    {
        return Arr::get($this->config, 'contexts.classes');
    }

    public function getAnalyzerCommands(): array
    {
        return Arr::get($this->config, 'runtime.analyzers');
    }

    public function getIntentDefaultRoute(Router $router): IntentRoute
    {
        if (!isset($this->defIntentRoute)) {
            $this->defIntentRoute = new IntentRoute($this, $router);
            $this->defIntentRoute
                ->action(function (Context $context, Intent $intent) {
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

    public function getAnalyzerMark(): string
    {
        return '/';
    }


}