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
class AliasesTest extends AbsAliases
{
    public static function preload(): void
    {
        self::setAlias('test', 't');
        self::setAlias('foo', 'f');
        self::setAlias('bar', 'b');
    }
}