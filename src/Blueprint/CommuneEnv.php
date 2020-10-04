<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Support\Utils\StringUtils;


/**
 * 这里定义 Commune 项目的各种常量.
 * 允许通过全局来设置, 并且全局获取之.
 * 只能定义一次, 而且有默认值.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CommuneEnv
{
    const DEBUG = 'COMMUNE_DEBUG';
    const RESET_REGISTRY = 'COMMUNE_RESET_REGISTRY';
    const BASE_PATH = 'COMMUNE_BASE_PATH';
    const RUNTIME_PATH = 'COMMUNE_RUNTIME_PATH';
    const RESOURCE_PATH = 'COMMUNE_RESOURCE_PATH';
    const CONFIG_PATH = 'COMMUNE_CONFIG_PATH';
    const LOG_PATH = 'COMMUNE_LOG_PATH';
    const LOADING_RESOURCE = 'COMMUNE_LOADING_RESOURCE';

    /*------- path ------*/

    /**
     * Commune 项目的根目录
     * @return string
     */
    public static function getBasePath() : string
    {
        return self::get(
            self::BASE_PATH,
            realpath(__DIR__ . '/../../demo')
        );
    }

    public static function defineBathPath(string $path) : void
    {
        self::set(self::BASE_PATH, $path);
    }


    /**
     * 配置文件所在路径
     * @return string
     */
    public static function getConfigPath() : string
    {
        return self::get(
            self::CONFIG_PATH,
            StringUtils::gluePath(
                self::getBasePath(),
                'config'
            )
        );
    }

    public static function defineConfigPath(string $path) : void
    {
        self::set(self::CONFIG_PATH, $path);
    }




    /*------- runtime path ------*/

    /**
     * Runtime 所在目录. 这里存放 pid/文件缓存等等.
     * @return string
     */
    public static function getRuntimePath() : string
    {
        return self::get(
            self::RUNTIME_PATH,
            StringUtils::gluePath(
                self::getBasePath(),
                'runtime'
            )
        );
    }

    public static function defineRuntimePath(string $path) : void
    {
        self::set(self::RUNTIME_PATH, $path);
    }


    /*------- log path ------*/


    /**
     * 日志所在路径. 默认是 runtime/log
     * @return string
     */
    public static function getLogPath() : string
    {
        return self::get(
            self::LOG_PATH,
            StringUtils::gluePath(
                self::getRuntimePath(),
                'log'
            )
        );
    }

    public static function defineLogPath(string $path) : void
    {
        if (!is_dir($path)) {
            throw new CommuneLogicException("path [$path] is invalid dir");
        }
        self::set(self::LOG_PATH, $path);
    }


    /*------- path ------*/

    /**
     * Resource 资源文件所在路径.
     * 资源是项目启动时可以加载的数据.
     * 和 Runtime 不一样, 资源文件在运行中是不应该修改的.
     * 项目也可以把各种数据 dump 到资源文件中, 方便用文件的形式传递和版本控制.
     *
     * @return string
     */
    public static function getResourcePath() : string
    {
        return self::get(
            self::RESOURCE_PATH,
            self::getBasePath() . '/resources'
        );
    }

    public static function defineResourcePath(string $path) : void
    {
        if (!is_dir($path)) {
            throw new CommuneLogicException("path [$path] is invalid dir");
        }
        self::set(self::RESOURCE_PATH, $path);
    }


    /*------- debug ------*/

    /**
     * 是否在 debug 状态下运行.
     * Debug 状态会增加许多性能消耗较大的解析环节.
     * @return bool
     */
    public static function isDebug() : bool
    {
        return self::get(self::DEBUG, false);
    }

    public static function defineDebug(bool $debug) : void
    {
        self::set(self::DEBUG, $debug);
    }

    /*------- mind ------*/

    /**
     * 是否重置注册表. 如果重置注册表, 则所有注册表理应在启动时主动清空一遍.
     * 这个参数要非常慎用. 是毁灭性的效果.
     * @return bool
     */
    public static function isResetRegistry() : bool
    {
        return self::get(self::RESET_REGISTRY, false);
    }

    public static function defineResetMind(bool $boolean) : void
    {
        self::set(self::RESET_REGISTRY, $boolean);
    }

    /*------- load resource ------*/

    /**
     * 是否加载 Resource 资源.
     * 启动时主动加载资源, 会产生大量的 IO 查询资源是否存在, 是否可以被覆盖等.
     * 所以不建议每次启动都加载.
     *
     * 但 isResetRegistry 的时候, loading Resource 一定为 True
     *
     * @return bool
     */
    public static function isLoadingResource() : bool
    {
        return self::isResetRegistry() || self::get(self::LOADING_RESOURCE, false);
    }
    
    
    public static function defineLoadingResource(bool $loading) : void
    {
        self::set(self::LOADING_RESOURCE, $loading);
    }

    /*------- private ------*/

    private static function get(string $name, $default)
    {
        if (defined($name)) {
            return constant($name);
        }

        define($name, $default);
        return $default;
    }

    private static function set(string $name, $value) : void
    {
        if (defined($name)) {
            throw new CommuneLogicException("constant $name already defined!");
        }
        define($name, $value);
    }

}