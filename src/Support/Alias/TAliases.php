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
trait TAliases
{
    private static $originToAlias = [];

    private static $aliasToOrigin = [];

    public static function setAlias(string $origin, string $alias) : void
    {
        self::$originToAlias[$origin] = $alias;
        self::$aliasToOrigin[$alias] = $origin;
    }

    public static function getOriginFromAlias(string $alias) : string
    {
        return self::$aliasToOrigin[$alias] ?? $alias;
    }

    public static function getAliasOfOrigin(string $origin) : string
    {
        return self::$originToAlias[$origin] ?? $origin;
    }


}