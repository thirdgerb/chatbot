<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Alias;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsAliases
{
    const FUNC_GET_ORIGIN = 'getOriginFromAlias';

    const FUNC_GET_ALIAS = 'getAliasOfOrigin';

    /**
     * @var string[][]
     */
    private static $originToAlias = [];

    /**
     * @var string[][]
     */
    private static $aliasToOrigin = [];

    /**
     * @var bool[]
     */
    private static $loaded = [];

    abstract public static function preload() : void;

    /**
     * @param string $origin
     * @param string $alias
     */
    public static function setAlias(string $origin, string $alias) : void
    {
        $class = static::class;
        self::$originToAlias[$class][$origin] = $alias;
        self::$aliasToOrigin[$class][$alias] = $origin;
    }

    public static function boot() : void
    {
        $class = static::class;
        if (empty(self::$loaded[$class])) {
            static::preload();
            self::$loaded[$class] = true;
        }
    }

    final public static function getOriginFromAlias(string $alias) : string
    {
        self::boot();
        return self::$aliasToOrigin[static::class][$alias] ?? $alias;
    }

    final public static function getAliasOfOrigin(string $origin) : string
    {
        self::boot();
        return self::$originToAlias[static::class][$origin] ?? $origin;
    }


}