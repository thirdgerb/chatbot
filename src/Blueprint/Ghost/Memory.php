<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use ArrayAccess;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 用于存储的记忆单元.
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

    public function isChanged() : bool;

    public function isLongTerm() : bool;
}