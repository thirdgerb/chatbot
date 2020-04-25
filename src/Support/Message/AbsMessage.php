<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;

use Commune\Support\Babel\BabelSerializable;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Struct\AStruct;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsMessage extends AStruct implements Message, Injectable
{
    use TInjectable;

    private static $docComments = [];

    public function toTransferArr(): array
    {
        $data = $this->toArray();
        $relations = [];
        foreach (static::getRelationNames() as $name) {
            $relations[$name] = $data[$name] ?? [];
            unset($data[$name]);
        }

        $result = ['attrs' => $data];
        if (!empty($relations)) {
            $result['relations'] = $relations;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return BabelSerializable|null
     */
    public static function fromTransferArr(array $data): ? BabelSerializable
    {
        $info = $data['attrs'] ?? [];
        $relations = $data['relations'] ?? [];
        return static::create($info + $relations);
    }

    public static function getTransferId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    public function isProtocal(string $protocalName): bool
    {
       return class_exists($protocalName)
           && is_a($protocalName, Protocal::class, TRUE)
           && is_a($this, $protocalName, TRUE);
    }

    public function toProtocal(string $protocalName): ? Protocal
    {
        if ($this instanceof Protocal && $this->isProtocal($protocalName)) {
            return $this;
        }
        return null;
    }

    final public static function getProtocals(): array
    {
        return static::getInterfacesOf(Protocal::class);
    }

    final public function getInterfaces(): array
    {
        return static::getInterfacesOf(Message::class);
    }

    public static function getDocComment(): string
    {
        $self = static::class;
        if (isset(self::$docComments[$self])) {
            return self::$docComments[$self];
        }

        // 自己的 property 注解.
        $selfR = new \ReflectionClass(static::class);
        $selfProps = StringUtils::fetchVariableAnnotationsWithType(
            $selfR->getDocComment(),
            '@property',
            false
        );

        // 协议的 property 注解.
        $protocals = static::getProtocals();
        $protocalProps = [];

        foreach ($protocals as $protocal) {
            if ($protocal === $self) {
                continue;
            }

            $r = new \ReflectionClass($protocal);

            $protocalProps[$protocal] = StringUtils::fetchVariableAnnotationsWithType($r->getDocComment(), '@property', false);
        }

        // 格式化
        $selfPropsMap = [];
        foreach ($selfProps as list($name, $type, $desc)) {
            $selfPropsMap[$name] = $type;
        }

        // 格式化.
        $protocalPropsMap = [];
        foreach ($protocalProps as $protocal => $props) {
            foreach($props as list($name, $type, $desc)) {
                $protocalPropsMap[$protocal][$name] = $type;
            }
        }

        if (!empty($selfPropsMap)) {
            $protocalPropsMap[$self] = $selfPropsMap;
        }

        // 先检查协议之间是否有冲突. 子协议不允许和父协议不一样.
        $realProps = [];
        foreach ($protocalPropsMap as $protocal => $props) {
            foreach($props as $name => $type) {
                if (!isset($realProps[$name])) {
                    $realProps[$name] = $type;
                    continue;
                }

                if ($realProps[$name] !== $type) {
                    throw new \LogicException(
                        'message class '
                        . static::class
                        . 'get conflict protocal definition, '
                        . "field \"$name\" has two type \"$type\" and \"{$realProps[$name]}\""
                    );
                }
            }
        }

        $doc = '';

        foreach ($realProps as $name => $type) {
            $doc .= "@property $type $" . $name . "\n";
        }

        return self::$docComments[$self] = $doc;
    }
}