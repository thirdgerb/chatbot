<?php

/**
 * Class ContextWrapper
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ArrayWrapper extends Collection
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
     * ArrayWrapper constructor.
     * @param array|\ArrayAccess $data
     * @param string $prevKey
     * @param \ArrayAccess $prev
     */
    public function __construct(
        $data,
        \ArrayAccess $prev = null,
        $prevKey = null
    )
    {
        if (!Arr::accessible($data)) {
            //todo
            throw new \InvalidArgumentException();
        }

        parent::__construct($data);
        $this->prev = $prev;
        $this->prevKey = $prevKey;
    }

    public function offsetGet($offset)
    {
            $val = $this->get($offset);
            if (!isset($val)) {
                return null;
            }
            return Arr::accessible($val)
                ? new ArrayWrapper($val, $this, $offset)
                : $val;
    }

    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);
        $this->refreshPrev();
    }

    public function offsetUnset($offset)
    {
        parent::offsetUnset($offset);
        $this->refreshPrev();
    }

    protected function refreshPrev()
    {
        if (isset($this->prev)) {
            $this->prev[$this->prevKey] = $this->data;
        }
    }

}