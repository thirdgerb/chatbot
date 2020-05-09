<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Arr;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TArrayData
{
    /**
     * @var array
     */
    protected $_data = [];

    protected $_changed = false;

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
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $this->_changed = true;
        unset($this->_data[$offset]);
    }


}