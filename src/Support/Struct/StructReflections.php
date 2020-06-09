<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;

use Commune\Support\Utils\StringUtils;


/**
 * 获取 Struct 对象的反射信息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StructReflections
{
    /**
     * @var StructFieldReflector[][]
     */
    protected static $validators = [];

    /**
     * 获得字段的 Reflection, 来自于 Struct::getDocComment()
     * @param string $className
     * @param string $fieldName
     * @return StructFieldReflector|null
     */
    public static function getFieldReflector(
        string $className,
        string $fieldName
    ) : ? StructFieldReflector
    {
        static::register($className);
        return static::$validators[$className][$fieldName] ?? null;
    }

    /**
     * @param string $className
     * @return StructFieldReflector[]
     */
    public static function getAllFieldReflectors(string $className) : array
    {
        static::register($className);
        return static::$validators[$className] ?? [];
    }

    /**
     * 类是否已经注册了.
     * @param string $className
     * @return bool
     */
    public static function isRegistered(string $className) : bool
    {
        return isset(static::$validators[$className]);
    }

    /**
     * 注册一个 Struct 类.
     * @param string $className
     */
    public static function register(string $className) : void
    {
        if (isset(static::$validators[$className])) {
            return;
        }

        $doc = call_user_func([$className, 'getDocComment']);
        static::registerClassByDoc($className, $doc);
    }

    /**
     * 使用 DocComment 来注册目标 Struct 类
     * @param string $className
     * @param string $doc
     */
    public static function registerClassByDoc(string $className, string $doc) : void
    {
        if (!is_a($className, Struct::class, TRUE)) {
            $expect = Struct::class;
            throw new InvalidStructException("reflection class must be subclass of $expect, $className given.");
        }

        // 从注解中获取变量的定义
        $defines = StringUtils::fetchVariableAnnotationsWithType($doc, '@property', false);

        $fields = [];

        foreach ($defines as list($name, $types, $desc)) {
            // 跳过 getter 方法
            $getter = constant($className.'::GETTER_PREFIX') . $name;
            if (method_exists($className, $getter)) {
                continue;
            }
            $fields[$name] = empty($types) ? [] : explode('|', $types);
        }

        $validators = [];

        foreach ($fields as $field => $rules) {
            $validators[$field] = new IStructFieldReflector(
                $className,
                $field,
                $rules,
                call_user_func([$className, 'getRelationClass'], $field),
                call_user_func([$className, 'isListRelation'], $field)
            );
        }

        static::$validators[$className] = $validators;
    }

    public static function parse(string $className, array $data) : array
    {
        static::register($className);
        $validators = static::$validators[$className] ?? null;
        if (empty($validators)) {
            return $data;
        }

        foreach ($data as $name => $value) {
            if (isset($validators[$name])) {
                $data[$name] = $validators[$name]->filterValue($data[$name]);
            }
        }

        return $data;
    }

    /**
     * 校验一个数组是否符合  Struct 的定义.
     *
     * @param string $className
     * @param array $data
     * @return null|string
     */
    public static function validate(string $className, array $data) : ? string
    {
        static::register($className);

        $validators = static::$validators[$className] ?? null;

        if (is_null($validators)) {
            throw new InvalidStructException("$className default validators not found.");
        }

        foreach ($validators as $field => $validator) {
            $error = $validator->validateValue($data[$field] ?? null);
            if (isset($error)) {
                return $error;
            }
        }

        return null;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}