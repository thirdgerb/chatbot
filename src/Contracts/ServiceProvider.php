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
use Commune\Support\Struct\AbsStruct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ServiceProvider extends AbsStruct
{
    /**
     * Provider 的唯一 ID, 可以根据实际情况重写.
     * @return string
     */
    public function getId(): string
    {
        return static::class;
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