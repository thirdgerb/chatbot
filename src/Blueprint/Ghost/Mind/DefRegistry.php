<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind;

use Commune\Blueprint\Ghost\Definition\Def;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;

/**
 * 存放各种多轮对话逻辑单元的仓库.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefRegistry
{
    public function reload() : void;

    public function hasDef(string $defName) : bool;

    /**
     * @param string $defName
     * @return Def
     * @throws DefNotDefinedException
     */
    public function getDef(string $defName) : Def;

    public function registerDef(Def $def) : void;

    public function countByPrefix(string $prefix = '') : int;

    public function getNamesByPrefix(string $prefix = '') : array;

}