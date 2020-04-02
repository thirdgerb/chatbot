<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\TBabelSerializable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMessage implements Message
{
    use ArrayAbleToJson, TBabelSerializable;

    /**
     * 毫秒级的时间戳.
     * @var int
     */
    protected $createdAt;

    /**
     * 可以做依赖注入的对象名
     * @var string[][]
     */
    private static $_interfaces = [];

    /**
     * AMessage constructor.
     * @param float $createdAt
     */
    public function __construct(float $createdAt = null)
    {
        $this->createdAt = $createdAt ?? microtime(true);
    }

    public function toArray(): array
    {
        return [
            'type' => static::getSerializableId(),
            'data' => $this->toSerializableArray()[0],
        ];
    }

    public function getCreatedAt() : int
    {
        return $this->createdAt
            ?? $this->createdAt = time();
    }


    final public function getInterfaces(): array
    {
        $class = static::class;

        if (isset(self::$_interfaces[$class])) {
            return self::$_interfaces[$class];
        }

        $r = new \ReflectionClass($class);

        // 根 message 类名
        $names[] = Message::class;
        // 所有 interface 里继承 message 的.
        foreach ( $r->getInterfaces() as $interfaceReflect ) {
            if ($interfaceReflect->isSubclassOf(Message::class)) {
                $names[] = $interfaceReflect->getName();
            }
        }

        // 抽象父类.
        do  {
            if ($r->isAbstract()) {
                $names[] = $r->getName();
            }


        } while ($r = $r->getParentClass());

        // 当前类名
        $names[] = $r->getName();

        return self::$_interfaces[$class] = $names;
    }

}