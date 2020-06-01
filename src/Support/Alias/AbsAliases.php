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
class AbsAliases
{
    /**
     * @var string[]
     */
    protected static $originToAlias = [];

    /**
     * @var string[]
     */
    protected static $aliasToOrigin = [];

    /**
     * @var bool
     */
    protected static $loaded;

    abstract public static function preload() : void;

    /**
     * @param string $origin
     * @param string $alias
     */
    public static function setAlias(string $origin, string $alias) : void
    {
        self::$originToAlias[$origin] = $alias;
        self::$aliasToOrigin[$alias] = $origin;
    }

    public static function boot() : void
    {
        if (!self::$loaded) {
            self::preload();
            self::$loaded = true;
        }
    }

    public static function getOriginFromAlias(string $alias) : string
    {
        self::boot();
        return self::$aliasToOrigin[$alias] ?? $alias;
    }

    public static function getAliasOfOrigin(string $origin) : string
    {
        self::boot();
        return self::$originToAlias[$origin] ?? $origin;
    }


}