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

use Commune\Support\Babel\Babel;
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


    /*------ config ------*/

    protected $transferNoEmptyRelations = true;

    protected $transferNoEmptyData = true;

    /*------ inner ------*/

    private static $docComments = [];

    public function toTransferArr(): array
    {
        $data = $this->_data;
        $stub = static::stub();

        // 去掉完全一致的数据, 不需要存储.
        foreach ($stub as $key => $val) {

            if (!array_key_exists($key, $data)) {
                continue;
            }

            $dataVal = $data[$key];

            if (
                // 条件1 : 空值
                empty($dataVal)
                // 条件2 : 简单值
                && (is_scalar($dataVal) || is_array($dataVal) || is_null($dataVal))
                // 条件3 : 必须和原来的值相等.
                && $dataVal === $val
            ) {
                unset($data[$key]);
                continue;
            }

            if (
                $dataVal instanceof self
                && $this->transferNoEmptyRelations
                && $dataVal->isEmpty()
            ) {
                unset($data[$key]);
            }

        }

        // relations
        $relations = [];
        $relationNames = static::getRelationNames();
        if (!empty($relationNames)) {
            foreach ($relationNames as $name) {
                $relationVal = $data[$name] ?? null;
                if (empty($relationVal)) {
                    continue;
                }

                if (static::isListRelation($name)) {
                    $relations[$name] = array_map(function($each) {
                        return Babel::getResolver()->encodeToArray($each);
                    }, $relationVal);
                } else {
                    $relations[$name] = Babel::getResolver()->encodeToArray($relationVal);
                }
                unset($data[$name]);
            }
        }
        $result = [];

        if (!empty($data) || !$this->transferNoEmptyData) {
            $result['attrs'] = $data;
        }

        if (!empty($relations) && $this->transferNoEmptyData) {
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

        foreach ($relations as $name => $value) {

            $info[$name] = static::isListRelation($name)
                ? array_map(
                    function($element){
                        return Babel::getResolver()->decodeFromArray($element);
                    },
                    $value
                )
                : Babel::getResolver()->decodeFromArray($value);
        }

        return static::create($info + $relations);
    }

    public static function getTransferId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    final public static function getProtocals(): array
    {
        return static::getInterfacesOf(
            Protocal::class,
            false,
            true,
            false
        );
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