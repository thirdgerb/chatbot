<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Memory;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\Babel;
use Commune\Support\Babel\BabelSerializable;


/**
 * 记忆体的实现.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRecollection implements Recollection
{
    use ArrayAbleToJson;


    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var bool
     */
    protected $_longTerm;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var bool
     */
    protected $_changed = true;

    /*---- cached ----*/

    protected $_expire = false;

    /**
     * IRecollection constructor.
     * @param string $id
     * @param string $name
     * @param bool $longTerm
     * @param array $data
     */
    public function __construct(string $id, string $name, bool $longTerm, array $data)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_longTerm = $longTerm;
        $this->_data = array_map([$this, 'parseValue'], $data);
    }

    /**
     * 检查参数是否合法.
     * Recollection 目前只允许 scalar, 纯数组, Stub 三种数据.
     * 是否要允许 Stub 数组, 暂时还没想清楚(2020-4-27)
     *
     * @param $value
     * @param bool $allowStub
     * @return mixed
     */
    public function parseValue($value, bool $allowStub = true)
    {
        if (is_scalar($value)) {
            return $value;
        }

        // 递归地检查数组.
        if (is_array($value)) {
            foreach ($value as $name => $val) {
                // 数组里不允许再有 Stub
                $value[$name] = $this->parseValue($val, false);
            }
            return $value;
        }

        // 允许作为 Stub 传入.
        if ($value instanceof Stub && $allowStub) {
            return $value;
        }

        throw new InvalidArgumentException(
            __METHOD__,
                'value',
            'only scalar, pure array, Commune\Blueprint\Ghost\Memory\Stub allowed'
            );
    }

    /*---- Array Access ----*/

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->_data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->_changed = true;
        $this->_data[$offset] = $this->parseValue($value);
    }

    public function offsetUnset($offset)
    {
        $this->_changed = true;
        unset($this->_data[$offset]);
    }

    public function toData(): array
    {
        return $this->_data;
    }


    /**
     * 合并数据
     * @param array $data
     */
    public function mergeData(array $data): void
    {
        foreach ($data as $name => $val) {
            $this->offsetSet($name, $val);
        }
    }

    /**
     * 重置数据.
     * @param array|null $data
     */
    public function resetData(array $data = null): void
    {
        $this->_data = array_map([$this, 'parseValue'], $data);
    }


    /*---- babel ----*/

    public function toTransferArr(): array
    {
        return [
            'id' => $this->_id,
            'name' => $this->_name,
            'longTerm' => $this->_longTerm,
            'data' => $this->toArray(),
        ];
    }

    public static function fromTransferArr(array $data): ? BabelSerializable
    {
        // 反序列化检查. 会不会导致故意存储为 json 的字符串...没了.
        $_data = $data['data'] ?? [];
        $_data = array_map(function($value){
            if (!is_string($value)) {
                return $value;
            }

            return Babel::unserialize($value) ?? $value;
        }, $_data);

        $obj = new static(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['longTerm'] ?? false,
            $_data
        );
        $obj->_changed = false;
        return $obj;
    }

    public static function getTransferId(): string
    {
        return static::class;
    }

    /*---- serialize ----*/

    public function __sleep()
    {
        return [
            '_id',
            '_name',
            '_longTerm',
            '_data',
        ];
    }

    public function __wakeup()
    {
        $this->_changed = false;
    }

    /*---- toArray ----*/

    /**
     * 反映的是 Recollection 的数据.
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function($value) {
            return $value instanceof Stub ? $value->toArray() : $value;
        }, $this->_data);
    }


    /*---- getter ----*/

    public function getId(): string
    {
        return $this->_id;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function isLongTerm(): bool
    {
        return $this->_longTerm;
    }


    public function isChanged(): bool
    {
        return $this->_changed;
    }

    /*---- cachable ----*/

    public function isCaching(): bool
    {
        return $this->_changed;
    }

    public function isExpiring(): bool
    {
        return $this->_expire;
    }

    public function expire(): void
    {
        $this->_expire = true;
    }


    public function getCachableId(): string
    {
        return $this->getId();
    }

    /*---- memorable ----*/

    public function toStub(): Stub
    {
        return new RecStub(['id' => $this->getId()]);
    }

    /*---- savable ----*/

    public function getSavableId(): string
    {
        return $this->getId();
    }

    public function isSaving(): bool
    {
        return $this->isChanged() && $this->isLongTerm();
    }

    public function __destruct()
    {
        $this->_data = [];
    }

}