<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct\Reflection;

use Commune\Support\Struct\InvalidStructException;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;


/**
 * 获取 Struct 对象的反射信息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AnnotationFactory
{
    /**
     * 使用 DocComment 来注册目标 Struct 类
     *
     * @param string $className
     * @param bool $strict
     * @param string|null $doc
     * @return StructReflection
     */
    public static function create(string $className, bool $strict, string $doc) : StructReflection
    {
        if (!is_a($className, Struct::class, TRUE)) {
            $expect = Struct::class;
            throw new InvalidStructException("reflection class must be subclass of $expect, $className given.");
        }

        $stub = call_user_func([$className, Struct::FUNC_STUB]);

        // 从注解中获取变量的定义
        $defines = StringUtils::fetchVariableAnnotationsWithType($doc, '@property', false);

        $fields = [];

        foreach ($defines as list($name, $types, $desc)) {
            // 跳过 getter 方法
            $getter = constant($className.'::GETTER_PREFIX') . $name;

            if (method_exists($className, $getter)) {
                continue;
            }

            $rules = empty($types) ? [] : explode('|', $types);
            $default = $stub[$name] ?? null;

            $fields[$name] = [$rules, $desc, $default];
        }

        $properties = [];

        foreach ($fields as $field => list($rules, $desc, $default)) {
            $properties[] = new IStructProperty(
                $className,
                $field,
                $rules,
                call_user_func([$className, 'getRelationClass'], $field),
                call_user_func([$className, 'isListRelation'], $field),
                $default,
                $desc
            );
        }

        return new IStructReflection(
            $className,
            $strict,
            $properties
        );
    }

}