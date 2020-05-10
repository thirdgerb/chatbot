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

    protected static function getInterfacesOf(
        string $baseType,
        bool $includeBasic = true,
        bool $includeSelf = true,
        bool $includeAbstract = true
    ): array
    {
        $class = static::class;
        $searchId = $class
            . intval($includeBasic)
            . intval($includeSelf)
            . intval($includeAbstract);

        if (isset(self::$_interfaces[$searchId][$baseType])) {
            return self::$_interfaces[$searchId][$baseType];
        }

        $r = new \ReflectionClass($class);
        $names = [];

        // 根 message 类名
        if ($includeBasic) {
            $names[] = $baseType;
        }

        // 所有 interface 里继承 message 的.
        foreach ( $r->getInterfaces() as $interfaceReflect ) {
            if ($interfaceReflect->isSubclassOf($baseType)) {
                $names[] = $interfaceReflect->getName();
            }
        }

        // 抽象父类.
        do  {
            if (
                $r->isSubclassOf($baseType)
                && ($r->isInterface() || ($r->isAbstract() && $includeAbstract))
            ) {
                $names[] = $r->getName();
            }

        } while ($r = $r->getParentClass());

        // 当前类名
        if ($includeSelf) {
            $names[] = $class;
        }

        return self::$_interfaces[$searchId][$baseType] = $names;
    }


}