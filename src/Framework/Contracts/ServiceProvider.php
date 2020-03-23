<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;

use Commune\Container\ContainerContract;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
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
     * boot 时传递进来的实例, 很可能和register 的时候不是同一个实例.
     * @param ContainerContract $app
     */
    abstract public function boot($app);

    /**
     * @return mixed
     */
    abstract public function register();

}