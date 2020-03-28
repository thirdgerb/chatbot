<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Babel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Babel
{
    /**
     * @var BabelResolver
     */
    protected static $resolver;

    public static function getResolver() : BabelResolver
    {
        return static::$resolver ?? (static::$resolver = new JsonResolver());
    }

    public static function setResolver(? BabelResolver $resolver) : void
    {
        static::$resolver = $resolver;
    }

}