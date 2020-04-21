<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Babel
{
    /**
     * @var BabelResolver
     */
    private static $resolver;

    /**
     * 转化到字符串这一步, 更进一步的转化就不由 Babel 实现了
     * @param Transfer $transfer
     * @return string
     */
    public static function serialize(Transfer $transfer) : string
    {
        return self::$resolver->serialize($transfer);
    }

    /**
     * 从字符串转化为 Transfer 对象.
     * @param string $serialized
     * @return Transfer|null
     */
    public static function unSerialize(string $serialized) : ? Transfer
    {
        return self::$resolver->unSerialize($serialized);
    }

    public static function getResolver() : BabelResolver
    {
        return self::$resolver;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

}