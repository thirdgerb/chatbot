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
    const FUNC_SERIALIZE = 'serialize';

    const FUNC_UN_SERIALIZE = 'unserialize';

    /**
     * @var BabelResolver
     */
    private static $resolver;

    public static function getResolver() : BabelResolver
    {
        return self::$resolver ?? (self::$resolver = new JsonResolver());
    }

    public static function setResolver(? BabelResolver $resolver) : void
    {
        self::$resolver = $resolver;
    }


    /**
     * 转化到字符串这一步, 更进一步的转化就不由 Babel 实现了
     *
     * @param mixed $serializable
     * @return string
     */
    public static function serialize($serializable) : string
    {
        return self::getResolver()->serialize($serializable);
    }

    /**
     * 从字符串转化为对象.
     * @param string $serialized
     * @return mixed|null
     */
    public static function unserialize(string $serialized)
    {
        return self::getResolver()->unserialize($serialized);
    }

}