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
    private static $docComments = [];

    public static function getRecursivelyPropertyDoc(string $className) : string
    {
        if (isset(self::$docComments[$className])) {
            return self::$docComments[$className];
        }

        // 自己的 property 注解.
        $r = new \ReflectionClass($className);
        $reflections = [];

        // 抽象父类.
        do  {
            if ($r->isSubclassOf(Struct::class)) {
                array_unshift($reflections, $r);

                // 所有 interface 里继承 message 的.
                foreach ( $r->getInterfaces() as $interfaceReflect ) {
                    if ($interfaceReflect->isSubclassOf(Struct::class)) {
                        array_unshift($reflections, $r);
                    }
                }
            }

        } while ($r = $r->getParentClass());


        $realProps = [];

        foreach ($reflections as $reflection) {
            $props = StringUtils::fetchVariableAnnotationsWithType(
                $reflection->getDocComment(),
                '@property',
                false
            );

            foreach($props as list($name, $type, $desc)) {
                $realProps[$name] = [$type, $desc];
            }
        }

        $doc = '';
        foreach ($realProps as $name => list($type, $desc )) {
            $doc .= "@property $type $" . $name . " $desc\n";
        }

        return self::$docComments[$className] = $doc;
    }


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

        // 准备 stub 参数.
        $stub = call_user_func([$className, Struct::FUNC_STUB]);

        // 从注解中获取变量的定义
        $defines = StringUtils::fetchVariableAnnotationsWithType($doc, '@property', false);

        $fields = [];

        // 准备注解.
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

        // 从注解中先创建好 property
        foreach ($fields as $field => list($rules, $desc, $default)) {
            $properties[$field] = new IStructProperty(
                $className,
                $field,
                $rules,
                call_user_func([$className, Struct::FUNC_GET_RELATION_CLASS], $field),
                call_user_func([$className, Struct::FUNC_IS_LIST_RELATION], $field),
                $default,
                $desc
            );
        }

        // relations 补遗.
        $relationNames = call_user_func([$className, Struct::FUNC_GET_RELATIONS]);
        foreach ($relationNames as $relationName) {
            // 如果忘记定义了 Protocols
            if (!array_key_exists($relationName, $properties)) {

                $relationsClass = call_user_func([$className, Struct::FUNC_GET_RELATION_CLASS], $relationName);

                $properties[$relationName] = new IStructProperty(
                    $className,
                    $relationName,
                    [$relationsClass],
                    $relationsClass,
                    call_user_func([$className, Struct::FUNC_IS_LIST_RELATION], $relationName),
                    [],
                    ''
                );

            }
        }

        return new IStructReflection(
            $className,
            $strict,
            $properties
        );
    }

}