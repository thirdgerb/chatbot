<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts;

use Commune\Container\ContainerContract;
use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ServiceProvider extends AbsOption
{
    const IDENTITY = '';

    public static function relations(): array
    {
        return [];
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