<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Parameter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ParamDefs
{

    /**
     * @param array $definition
     * @return ParamDefs
     */
    public static function create(array $definition) : ParamDefs;

    /**
     * @return int
     */
    public function count() : int;

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
     * @param Param $param
     */
    public function addParam(Param $param) : void;

    /**
     * @return string[]
     */
    public function keys() : array;

    /**
     * @param array $values
     * @param bool $onlyDefined
     * @return array
     */
    public function parse(array $values, bool $onlyDefined = false) : array;

    /**
     * @return array
     */
    public function getDefaults() : array;

    /**
     * @return array
     */
    public function getDefinitions() : array;
}