<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\DI;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TInjectable
{
    protected static $_interfaces = [];

    protected static function getInterfacesOf(string $baseType): array
    {
        $class = static::class;

        if (isset(self::$_interfaces[$class][$baseType])) {
            return self::$_interfaces[$class][$baseType];
        }

        $r = new \ReflectionClass($class);

        // 根 message 类名
        $names[] = $baseType;
        // 所有 interface 里继承 message 的.
        foreach ( $r->getInterfaces() as $interfaceReflect ) {
            if ($interfaceReflect->isSubclassOf($baseType)) {
                $names[] = $interfaceReflect->getName();
            }
        }

        // 抽象父类.
        do  {
            if ($r->isAbstract() && $r->isSubclassOf($baseType)) {
                $names[] = $r->getName();
            }

        } while ($r = $r->getParentClass());

        // 当前类名
        $names[] = $r->getName();

        return self::$_interfaces[$class][$baseType] = $names;
    }


}