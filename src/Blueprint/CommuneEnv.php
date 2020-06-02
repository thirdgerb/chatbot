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
    const RESET_MIND = 'COMMUNE_RESET_MIND';

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

    public static function isResetMind() : bool
    {
        return self::get(self::RESET_MIND, false);
    }

    public static function defineResetMind(bool $boolean) : void
    {
        self::set(self::RESET_MIND, $boolean);
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