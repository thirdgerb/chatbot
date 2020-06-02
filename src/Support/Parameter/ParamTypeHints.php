<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Parameter;

use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\TypeUtils;

/**
 * 自定义的类型约束管理者.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ParamTypeHints
{
    /**
     * @var TypeHint[]
     */
    protected static $typeHints = [];

    /**
     * 注册一个自定义类型约束.
     * @param string $name
     * @param TypeHint $typeHint
     */
    public static function registerParser(string $name, TypeHint $typeHint) : void
    {
        self::$typeHints[$name] = $typeHint;
    }


    /**
     * 用一个类型来校验一个值.
     * 类型可以用 [] 结尾, 表示要求的是数组.
     * 例如 object[], string[], int[] 等等.
     *
     * @param string $type
     * @param $value
     * @return bool
     */
    public static function validate(string $type, $value) : bool
    {
        $params = [];
        if (strpos($type, ',') !== false) {
            $typeDefines = explode(',', $type);
            $type = array_shift($typeDefines);
            $params = $typeDefines;
        }

        $isList = TypeUtils::isListTypeHint($type);
        if ($isList) {
            $type = TypeUtils::pureListTypeHint($type);
        }

        if ($isList && !is_array($value)) {
            return false;
        }

        if (!$isList && is_array($value)) {
            return false;
        }

        // 自定义的 parser
        if (isset(static::$typeHints[$type])) {
            $typeHint = static::$typeHints[$type];
            $type = function($value) use ($typeHint, $params) {
                return $typeHint->validate($value, ...$params);
            };
        }

        return $isList
            ? TypeUtils::listValidate($type, $value)
            : TypeUtils::validate($type, $value);

    }

    public static function parse(string $type, $value)
    {
        $params = [];
        if (strpos($type, ',') !== false) {
            $typeDefines = explode(',', $type);
            $type = array_shift($typeDefines);
            $params = $typeDefines;
        }

        $isList = TypeUtils::isListTypeHint($type);

        if ($isList) {
            $type = TypeUtils::pureListTypeHint($type);
        }

        // 如果要求是数组, 传入参数不是数组时, 会自动转成数组
        if ($isList && !is_array($value)) {
            $value = ArrayUtils::wrap($value);
        }

        // 如果要求不是数组, 而传入是数组时, 会取第一个值.
        if (!$isList && is_array($value)) {
            $value = current($value);
        }

        if (isset(static::$typeHints[$type])) {
            $typeHint = static::$typeHints[$type];
            $type = function($value) use ($typeHint, $params) {
                return $typeHint->parse($value, ...$params);
            };
        }

        return $isList
            ? TypeUtils::listParser($type, $value)
            : TypeUtils::parse($type, $value);
    }

}