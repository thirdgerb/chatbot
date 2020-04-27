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
use Commune\Blueprint\Ghost\ClonerScope;
use Commune\Blueprint\Ghost\Memory\Memorable;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * 系统的记忆模块. 允许用类的方式, 定义自己所需要的记忆体.
 *
 * 例如用类名定义:
 * class MemoFoo extends AMemory {}
 *
 * 然后调用:
 * $foo = MemoFoo::find($cloner);
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMemory implements Memory
{
    use ArrayAbleToJson;

    const GETTER_PREFIX = '__get_';
    const SETTER_PREFIX = '__set_';

    /**
     * @var Recollection
     */
    protected $_recollection;

    /**
     * @var Cloner
     */
    protected $_cloner;

    protected function __construct(
        Cloner $cloner,
        Recollection $recollection
    )
    {
        $this->_cloner = $cloner;
        $this->_recollection = $recollection;
    }



    /*--- abstract ---*/

    /**
     * 指定记忆的默认作用域.
     *
     * @see ClonerScope
     * @return string[]
     */
    abstract public static function getScopes() : array;

    /**
     * 定义记忆体的默认值.
     * @return array
     */
    abstract public static function stub(): array;

    /**
     * 定义记忆体的名称.
     * @return string
     */
    abstract public static function getMemoryName(): string;



    /*--- find ---*/

    public static function find(Cloner $cloner): Memory
    {
        $id = static::makeId($cloner);
        $name = static::getMemoryName();
        $scopes = static::getScopes();

        $recollection = $cloner->runtime->findRecollection($id)
            ?? $cloner->runtime->createRecollection(
                $id,
                $name,
                !empty($scopes),
                static::stub()
            );

        return new static($cloner, $recollection);
    }


    public function getId(): string
    {
        return $this->_recollection->getId();
    }


    /*--- array access ---*/

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
        if (method_exists($this, $method = static::GETTER_PREFIX . $name)) {
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
        if (method_exists($this, $method = static::SETTER_PREFIX . $name)) {
            $this->{$method}($value);
            return;
        }
        $this->offsetSet($name ,$value);
    }

    /*--- array ---*/

    public function toArray(): array
    {
        return $this->_recollection->toArray();
    }

    /*--- stub ---*/

    public static function makeId(Cloner $cloner): string
    {
        return $cloner->scope->makeScopeId(
            $name = static::getMemoryName(),
            $scopes = static::getScopes()
        );
    }


    public function toStub(): Stub
    {
        return new MemStub(['className' => static::class]);
    }


}