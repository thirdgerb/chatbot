<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Memory;

use ArrayAccess;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 用于存储上下文变量的记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Memory extends
    ArrayAccess, // 数组式的调用
    ArrayAndJsonAble // 可转化为数组.
{
    /**
     * 记忆的 Id
     * @return string
     */
    public function getId() : string;

    /**
     * @return bool
     */
    public function isChanged() : bool;

    /**
     * @return bool
     */
    public function isLongTerm() : bool;

    /**
     * 返回持有数据的原始值.
     * @return array
     */
    public function toData() : array;

    /**
     * @return array
     */
    public function keys() : array;
}