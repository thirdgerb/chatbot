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
use Illuminate\Support\Arr;


/**
 * 类型检查和转换工具.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtils
{
    public static function normalizeClassName(string $className) : string
    {
        $className = StringUtils::namespaceSlashToDot($className);
        return strtolower($className);
    }

    public static function isListTypeHint(string $type) : bool
    {
        return substr($type, -2, 2) === '[]';
    }

    public static function pureListTypeHint(string $type) : string
    {
        return self::isListTypeHint($type)
            ? substr($type, 0 , -2)
            : $type;

    }

    public static function isA($value, string $className) : bool
    {
        return (is_object($value) || is_string($value))
            && is_a($value, $className, TRUE);
    }

    /**
     * 获取目标的类型
     * @param $value
     * @return string
     */
    public static function getType($value) : string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    public static function listTypeHintParse(string $type, $value) : array
    {
        $value = Arr::wrap($value);

        return array_map(function($val) use ($type) {
            return static::typeHintParse($type, $val);
        }, $value);

    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed $value
     */
    public static function typeHintParse(string $type, $value)
    {
        if (!is_scalar($value)) {
            return $value;
        }

        switch($type) {
            case 'mixed' :
                return $value;
            case 'string' :
                return strval($value);
            case 'bool' :
            case 'boolean' :
                return boolval($value);
            case 'int' :
            case 'integer' :
                return intval($value);
            case 'float' :
                return floatval($value);
            case 'double' :
                return doubleval($value);
            default:
                return $value;
        }
    }

    public static function listTypeHintValidate(string $type, $value) : bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value)) {
            return true;
        }

        foreach ($value as $val) {
            if (!self::typeHintValidate($type, $val)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 注解中常用类型的参数校验.
     *
     * @param string $type
     * @param $value
     * @return bool
     */
    public static function typeHintValidate(string $type, $value) : bool
    {
        if (class_exists($type)) {
            return is_object($value) && is_a($value, $type, TRUE);
        }

        switch($type) {
            case 'mixed' :
                return true;
            case 'null' :
                return is_null($value);
            case 'string' :
                return is_string($value);
            case 'bool' :
            case 'boolean' :
                return is_bool($value);
            case 'int' :
            case 'integer' :
                return is_int($value);
            case 'float' :
                return is_float($value) || is_int($value);
            case 'double' :
                return is_double($value) || is_int($value);
            case 'array' :
                return is_array($value);
            case 'callable' :
                return is_callable($value);
            default:
                return false;
        }

    }

}