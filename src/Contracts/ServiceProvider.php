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
    // 用于定义哪一个字段作为 provider 的 id
    const IDENTITY = '';

    const SCOPE_CONFIG = 'config';
    const SCOPE_PROC = 'proc';
    const SCOPE_REQ = 'req';


    /**
     * 定义 provider 默认的级别, 是配置级 config/进程级 proc/请求级 req
     * @return string
     */
    abstract public function getDefaultScope() : string;

    /**
     * 定义 provider 的默认属性.
     * @return array
     */
    abstract public static function stub(): array;

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

    /**
     * 定义 provider 的关联 option
     * @return array
     */
    public static function relations(): array
    {
        return [];
    }

}