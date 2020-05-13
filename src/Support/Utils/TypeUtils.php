<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Utils;
use Commune\Blueprint\Ghost\Context;


/**
 * 类型检查和转换工具.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtils
{

    /**
     * 获取目标的类型
     * @param $value
     * @return string
     */
    public static function getType($value) : string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    public static function parseContextClassToName(string $str) : string
    {
        $str = StringUtils::namespaceSlashToDot($str);
        return strtolower($str);
    }

    public static function isValidContextName(string $str) : bool
    {
        $pattern = '/^[a-z][a-z0-9]*(\.[a-z][a-z0-9]+)*$/';
        return (bool) preg_match($pattern, $str);
    }

    public static function isValidMemoryName(string $str) : bool
    {
        return self::isValidContextName($str);
    }

    public static function normalizeMemoryName(string $str) : string
    {
        return strtolower(StringUtils::namespaceSlashToDot($str));
    }

    public static function isValidStageFullName(string $str) : bool
    {
        return self::isValidIntentName($str);
    }

    public static function isValidIntentName(string $str) : bool
    {
        $pattern = '/^[a-z][a-z0-9]*(\.[a-z][a-z0-9]+)*(\.[a-z][a-z_0-9]+){0,1}$/';
        return (bool) preg_match($pattern, $str);
    }

    public static function isValidEntityName(string $str) : bool
    {
        return self::isValidContextName($str);
    }
}