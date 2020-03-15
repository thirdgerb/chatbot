<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Memory;

use Commune\FPHost\Blueprint\Dialog\Scope;
use Commune\FPHost\Blueprint\Mind\MemoryDef;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 具体的记忆信息. 可以用数组的方式进行存取.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends \ArrayAccess, ArrayAndJsonAble
{
    /**
     * 记忆点用于存储的唯一 ID
     * @return string
     */
    public function uuid() : string;

    /**
     * 记忆体的名称, 用于寻找 MemoryDef
     * @return string
     */
    public function memoryName() : string;

    /**
     * 创建时的作用域
     * @return Scope
     */
    public function createdScope() : Scope;

    /**
     * 最后更改时的作用域
     * @return Scope
     */
    public function updatedScope() : Scope;
}