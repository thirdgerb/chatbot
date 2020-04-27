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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Memorable;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Framework\Exceptions\SerializeForbiddenException;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TMemorable
{
    use ArrayAbleToJson;

    protected $_getter_prefix = '__get_';

    protected $_setter_prefix = '__set_';

    /**
     * @var Recollection
     */
    protected $_recollection;

    /**
     * @var Cloner
     */
    protected $_cloner;


    public function getId(): string
    {
        return $this->_recollection->getId();
    }

    /**
     * 合并数据
     * @param array $data
     */
    public function mergeData(array $data): void
    {
        $this->_recollection->mergeData($data);
    }

    /**
     * 重置数据.
     * @param array|null $data
     */
    public function resetData(array $data = null): void
    {
        $this->_recollection->resetData($data);
    }

    public function toData(): array
    {
        return $this->_recollection->toData();
    }

    /*----- arrayAble -----*/

    public function toArray(): array
    {
        $data = $this->_recollection->toData();
        // 递归地获取数组数据.
        return array_map(function($value) {
            if ($value instanceof Stub) {
                $value = $value->toMemorable($this->_cloner);
                if ($value instanceof Memorable) {
                    return $value->toArray();
                }
            }

            return $value;

        }, $data);
    }

    /*----- array access -----*/


    public function offsetExists($offset)
    {
        return $this->_recollection->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $value = $this->_recollection->offsetGet($offset);
        if ($value instanceof Stub) {
            return $value->toMemorable($this->_cloner);
        }
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Memorable) {
            $value = $value->toStub();
        }
        $this->_recollection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->_recollection->offsetUnset($offset);
    }

    /*--- getter & setter ---*/

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __get($name)
    {
        if (method_exists($this, $method = $this->_getter_prefix . $name)) {
            return $this->{$method};
        }
        return $this->offsetGet($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function __set($name, $value)
    {
        if (method_exists($this, $method = $this->_setter_prefix . $name)) {
            $this->{$method}($value);
            return;
        }
        $this->offsetSet($name ,$value);
    }

    public function __sleep()
    {
        throw new SerializeForbiddenException(static::class);
    }
}