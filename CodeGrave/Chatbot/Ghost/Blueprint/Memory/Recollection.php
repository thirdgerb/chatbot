<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Memory;

use Commune\Chatbot\Ghost\Blueprint\Dialog\Scope;
use Commune\Chatbot\Ghost\Blueprint\Session\SessionData;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 具体的记忆信息. 可以用数组的方式进行存取.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends \ArrayAccess, ArrayAndJsonAble, SessionData
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
     * 是否是长程记忆.
     * @return bool
     */
    public function isLongTerm() : bool;


    /**
     * 数据是否有修改. 修改了才要存储.
     * @return bool
     */
    public function isChanged() : bool;

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