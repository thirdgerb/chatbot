<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definition;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefRegistry
{

    public function reload() : void;

    public function hasDef(string $defName) : bool;

    public function getDef(string $defName) : Def;

    public function registerDef(Def $def) : void;

    public function countByPrefix(string $prefix = '') : int;

    public function getNamesByPrefix(string $prefix = '') : array;

}