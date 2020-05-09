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
 *
 * @mixin \ArrayAccess
 */
trait TArrayAccessToMutator
{

    protected $_getter_prefix = '__get_';

    protected $_setter_prefix = '__set_';

    public function __isset($name)
    {
        $method = $this->_getter_prefix . $name;
        if (method_exists($this, $method)) {
            $value = $this->{$method}();
            return isset($value);
        }

        return $this->offsetGet($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function __get($name)
    {
        $method = $this->_getter_prefix . $name;
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $method = $this->_setter_prefix . $name;
        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            $this->offsetSet($name, $value);
        }
    }


}