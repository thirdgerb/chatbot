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

/**
 * 类型检查和转换工具.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtils
{
    /**
     * @param string $className
     * @return string
     */
    public static function normalizeClassName(string $className) : string
    {
        $className = StringUtils::namespaceSlashToDot($className);
        return strtolower($className);
    }



    /**
     * @param string $type
     * @return bool
     */
    public static function isListTypeHint(string $type) : bool
    {
        return substr($type, -2, 2) === '[]';
    }

    /**
     * @param string $type
     * @return string
     */
    public static function pureListTypeHint(string $type) : string
    {
        return self::isListTypeHint($type)
            ? substr($type, 0 , -2)
            : $type;

    }

    /**
     * @param $value
     * @param string $className
     * @return bool
     */
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

    /**
     * @param string|callable $parser
     * @param $value
     * @return array
     */
    public static function listParser($parser, $value) : array
    {
        $value = ArrayUtils::wrap($value);

        return array_map(function($val) use ($parser) {
            return static::parse($parser, $val);
        }, $value);

    }

    /**
     * @param string|callable $parser
     * @param mixed $value
     * @return mixed $value
     */
    public static function parse($parser, $value)
    {
        if (is_callable($parser)) {
            return $parser($value);
        }

        if (!is_scalar($value)) {
            return $value;
        }

        switch($parser) {
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

    /**
     * @param $validator
     * @param $value
     * @param bool $strict
     * @return bool
     */
    public static function listValidate($validator, $value, bool $strict = false) : bool
    {
        if (!is_array($value)) {
            return false;
        }

        // 空数组的情况.
        if (empty($value)) {
            return true;
        }

        foreach ($value as $val) {
            if (!self::validate($validator, $val, $strict)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 注解中常用类型的参数校验.
     *
     * @param $type
     * @param $value
     * @param bool $strict
     * @return bool
     */
    public static function validate($type, $value, bool $strict = false) : bool
    {
        if (self::isListTypeHint($type)) {
            $type = self::pureListTypeHint($type);
            return self::listValidate($type, $value, $strict);
        }

        return self::standardTypeValidate($type, $value, $strict);
    }

    public static function standardTypeValidate(string $type, $value, bool $strict = false) : bool
    {
        if (is_callable($type)) {
            return $type($value);
        }

        if (class_exists($type)) {
            return is_object($value) && is_a($value, $type, TRUE);
        }

        switch($type) {
            case 'mixed' :
                return true;
            case 'null' :
                return is_null($value);
            case 'object' :
                return is_object($value);
            case 'string' :
                return $strict ? is_string($value) : self::maybeString($value);
            case 'bool' :
            case 'boolean' :
                return $strict ? is_bool($value) : self::maybeBool($value);
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

    public static function maybeString($value) : bool
    {
        return is_scalar($value)
            || is_null($value)
            || StringUtils::isString($value);
    }

    public static function maybeBool($value) : bool
    {
        return is_bool($value)
            || $value === 'true'
            || $value === 'false';
    }

    public static function requireFields(array $data, array $fields) : ? string
    {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return "$field is required";
            }
        }

        return null;
    }
}