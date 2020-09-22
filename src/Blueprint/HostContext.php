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

use Commune\Blueprint\Exceptions\Boot\CommuneNotRunningException;


/**
 * 可以用静态方法获取 Host 的唯一单例.
 * 通常在 Host 实例化时会注入到这个静态容器中.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HostContext
{
    private static $host;

    public static function setHost(Host $host) : void
    {
        self::$host = $host;
    }

    /**
     * @return Host
     */
    public static function getHost() : Host
    {
        if (isset(self::$host)) {
            return self::$host;
        }

        throw new CommuneNotRunningException(static::class . '::'. __FUNCTION__);
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }


}