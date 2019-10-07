<?php

/**
 * Class ServiceProvider
 * @package Commune\Container
 */

namespace Commune\Chatbot\Blueprint;

use Commune\Container\ContainerContract;

abstract class ServiceProvider
{

    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @var ContainerContract
     */
    protected $app;

    /**
     * ServiceProvider constructor.
     * @param ContainerContract $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function isProcessServiceProvider() : bool
    {
        return static::IS_PROCESS_SERVICE_PROVIDER;
    }

    /**
     * @param ContainerContract $app
     * @return static
     */
    public function withApp($app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * boot 时传递进来的实例, 很可能和register 的时候不是同一个实例.
     * @param ContainerContract $app
     */
    abstract public function boot($app);

    /**
     * @return mixed
     */
    abstract public function register();
}