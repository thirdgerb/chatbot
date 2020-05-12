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
class Aliases
{
    private static $aliases = [];

    public static function alias(string $name) : string
    {
        return self::$aliases[$name] ?? $name;
    }

    public static function setAlias(string $origin, string $alias) : void
    {
        self::$aliases[$alias] = $origin;
    }


}