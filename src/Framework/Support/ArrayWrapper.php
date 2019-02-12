<?php

/**
 * Class ContextWrapper
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Support;

use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Illuminate\Support\Arr;

class ArrayWrapper implements \ArrayAccess
{
    /**
     * @var ArrayWrapper
     */
    protected $prev;

    /**
     * @var string
     */
    protected $prevKey;


    /**
     * @var array|\ArrayAccess
     */
    protected $data;


    /**
     * ArrayWrapper constructor.
     * @param array|\ArrayAccess $data
     * @param string $prevKey
     * @param \ArrayAccess $prev
     */
    public function __construct(
        $data,
        \ArrayAccess $prev = null,
        string $prevKey = null
    )
    {
        if (!Arr::accessible($data)) {
            //todo
            throw new ChatbotException();
        }
        $this->data = $data;
        $this->prev = $prev;
        $this->prevKey = $prevKey;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            $val = $this->data[$offset];
            return is_array($val) ? new ArrayWrapper($val, $this, $offset) : $val;
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        if (isset($offset)) {
            $this->data[$offset] = $value;
        } else {
            $this->data[] = $value;
        }

        if ($this->prev) {
            $this->prev[$this->prevKey] = $this->data;
        } elseif(isset($this->prevKey)) {
            $this->prev->offsetSet($this->prevKey, $this->data);
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function toArray() : array
    {
        return $this->data;
    }

}