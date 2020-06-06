<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @method static serialize(array $data) : string
 * @method static unserialize(string $data) : array
 */
class ProcessSerializeManager
{
    /**
     * @var IProcessSerializer
     */
    private static $ins;

    final public static function getIns() : ProcessSerializer
    {
        return self::$ins ?? self::$ins = new IProcessSerializer();
    }

    final public static function setIns(ProcessSerializer $ins) : void
    {
        self::$ins = $ins;
    }

    public static function __callStatic($name, $arguments)
    {
        $ins = self::getIns();
        if (method_exists($ins, $name)) {
            return call_user_func_array([$ins, $name], $arguments);
        }

        throw new \BadMethodCallException("method $name not found");
    }

}