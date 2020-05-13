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
use Commune\Blueprint\Ghost\Memory\Memory;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TRecollection
{

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
     * @var Memory
     */
    protected $_memory;

    /**
     * @var Cloner
     */
    protected $_cloner;



    public function getId() : string
    {
        return $this->_id;
    }

    public function isLongTerm(): bool
    {
        return $this->_longTerm;
    }


    public function offsetExists($offset)
    {
        return $this->_memory->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $value = $this->_memory->offsetGet($offset);
        if ($value instanceof Cloner\ClonerInstanceStub) {
            $value = $value->toInstance($this->_cloner);
        }
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Cloner\ClonerInstance) {
            $value = $value->toInstanceStub();
        }
        $this->_memory->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->_memory->offsetUnset($offset);
    }

    public function toArray(): array
    {
        return $this->_memory->toArray();
    }


    public function __destruct()
    {
        $this->_cloner = null;
        $this->_memory = null;
    }
}