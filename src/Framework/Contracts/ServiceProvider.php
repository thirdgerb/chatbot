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
use Commune\Support\Structure;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ServiceProvider extends Structure
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @var ContainerContract
     */
    protected $app;

    /**
     * 是否是进程级的服务. 否则是请求级的服务.
     * @return bool
     */
    public function isProcessServiceProvider() : bool
    {
        return static::IS_PROCESS_SERVICE_PROVIDER;
    }

    /**
     * 初始化服务.
     * @param ContainerContract $app
     */
    abstract public function boot(ContainerContract $app) : void;

    /**
     * 注册服务到容器
     * @param ContainerContract $app
     */
    abstract public function register(ContainerContract $app) : void;

}