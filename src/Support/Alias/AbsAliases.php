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
    /**
     * @var string[][]
     */
    protected static $originToAlias = [];

    /**
     * @var string[][]
     */
    protected static $aliasToOrigin = [];

    /**
     * @var bool[]
     */
    protected static $loaded = [];

    abstract public static function preload() : void;

    /**
     * @param string $origin
     * @param string $alias
     */
    public static function setAlias(string $origin, string $alias) : void
    {
        $class = static::class;
        static::$originToAlias[$class][$origin] = $alias;
        static::$aliasToOrigin[$class][$alias] = $origin;
    }

    public static function boot() : void
    {
        $class = static::class;
        if (empty(static::$loaded[$class])) {
            static::preload();
            static::$loaded[$class] = true;
        }
    }

    public static function getOriginFromAlias(string $alias) : string
    {
        static::boot();
        return static::$aliasToOrigin[static::class][$alias] ?? $alias;
    }

    public static function getAliasOfOrigin(string $origin) : string
    {
        static::boot();
        return static::$originToAlias[static::class][$origin] ?? $origin;
    }


}