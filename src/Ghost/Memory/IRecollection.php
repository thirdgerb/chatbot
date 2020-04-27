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
     * @param string $_id
     * @param string $_name
     * @param bool $_longTerm
     * @param array $_data
     */
    public function __construct(string $_id, string $_name, bool $_longTerm, array $_data)
    {
        $this->_id = $_id;
        $this->_name = $_name;
        $this->_longTerm = $_longTerm;
        array_map([$this, 'checkValue'], $_data);

        $this->_data = $_data;
    }

    /**
     * 检查参数是否合法. Recollection 目前只允许 scalar, 纯数组, Stub 三种数据.
     *
     * @param $value
     * @param bool $allowStub
     */
    public function checkValue($value, bool $allowStub = true) : void
    {
        if (is_scalar($value)) {
            return;
        }

        // 递归地检查数组.
        if (is_array($value)) {
            foreach ($value as $name => $val) {
                // 数组里不允许再有 Stub
                $this->checkValue($val, false);
            }
            return;
        }

        // 允许作为 Stub 传入.
        if ($value instanceof Stub && $allowStub) {
            return;
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
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
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
        return 'rec:cah:' . $this->getId();
    }

    /*---- memorable ----*/

    public function toStub(): Stub
    {
        return new RecStub(['id' => $this->getId()]);
    }


    /*---- savable ----*/

    public function getSavableId(): string
    {
        return 'rec:sav:' . $this->getId();
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