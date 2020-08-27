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

    /*------- path ------*/

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
            throw new CommuneLogicException("path $path is invalid dir");
        }
        self::set(self::LOG_PATH, $path);
    }


    /*------- path ------*/

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
            throw new CommuneLogicException("path $path is invalid dir");
        }
        self::set(self::RESOURCE_PATH, $path);
    }


    /*------- debug ------*/

    public static function isDebug() : bool
    {
        return self::get(self::DEBUG, false);
    }

    public static function defineDebug(bool $debug) : void
    {
        self::set(self::DEBUG, $debug);
    }

    /*------- mind ------*/

    public static function isResetRegistry() : bool
    {
        return self::get(self::RESET_REGISTRY, false);
    }

    public static function defineResetMind(bool $boolean) : void
    {
        self::set(self::RESET_REGISTRY, $boolean);
    }

    /*------- private ------*/

    private static function get(string $name, $default)
    {
        return defined($name)
            ? constant($name)
            : $default;
    }

    private static function set(string $name, $value) : void
    {
        if (defined($name)) {
            throw new CommuneLogicException("constant $name already defined!");
        }
        define($name, $value);
    }

}