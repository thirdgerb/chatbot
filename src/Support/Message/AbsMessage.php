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

use Commune\Message\Abstracted\IComprehension;
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

    /**
     * @var bool  如果某个 relation 对象为空, 可设置 Babel 序列化时不包含该值. 则当前类的 stub 中应该有空的默认值.
     */
    protected $transferNoEmptyRelations = true;

    /**
     * @var bool  如果自身的数据为空, Babel 序列化时不传入 attr && relation 的结构, 以减少空间.
     */
    protected $transferNoEmptyData = true;

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
                // 条件3 : 必须和 stub 的默认值相等.
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
                // 如果某个关联数据不存在
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

    final public function getInterfaces(): array
    {
        return static::getInterfacesOf(Message::class);
    }
}