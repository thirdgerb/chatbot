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
use Commune\Blueprint\Ghost\Runtime\Cachable;
use Commune\Blueprint\Ghost\Runtime\Savable;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 记忆单元.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends
    ArrayAccess, // 数组式的调用
    ArrayAndJsonAble, // 可转化为数组.
    Cachable, // 可以缓存
    Savable
{
    /**
     * 记忆的 Id
     * @return string
     */
    public function getId() : string;

    /**
     * 记忆的名称.
     * @return string
     */
    public function getName() : string;

    /**
     * 是否要长期保存.
     * @return bool
     */
    public function isLongTerm() : bool;
}