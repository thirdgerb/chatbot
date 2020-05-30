<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ParamCollection
{

    /**
     * @param string $name
     * @return bool
     */
    public function hasParam(string $name) : bool;

    /**
     * @return Param[]
     */
    public function getAllParams() : array;

    /**
     * @param string $name
     * @return Param|null
     */
    public function getParam(string $name) : ? Param;

    /**
     * @return string[]
     */
    public function keys() : array;

    /**
     * @param array $values
     * @param bool $strict
     * @return array
     */
    public function parse(array $values, bool $strict = false) : array;

    /**
     * @return array
     */
    public function getDefaults() : array;

    /**
     * @return array
     */
    public function getDefinitions() : array;
}