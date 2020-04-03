<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Session;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionStorage
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    public function set(string $name, $value) : void;

    public function setAll(array $values) : void;

    public function getAll() : array;

    public function save() : void;

}