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
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StructReflections
{
    /**
     * @var StructFieldReflector[][]
     */
    protected static $validators = [];

    public static function getFieldReflector(
        string $className,
        string $fieldName
    ) : ? StructFieldReflector
    {
        static::register($className);
        return static::$validators[$className][$fieldName] ?? null;
    }

    public static function register(string $className) : void
    {
        if (!is_a($className, Struct::class, TRUE)) {
            $expect = Struct::class;
            throw new InvalidStructException("reflection class must be subclass of $expect, $className given.");
        }

        if (isset(static::$validators[$className])) {
            return;
        }

        $r = new \ReflectionClass($className);
        $doc = $r->getDocComment();

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

}