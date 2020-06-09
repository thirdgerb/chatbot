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

use Commune\Blueprint\Ghost\Cloner\ClonerInstance;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 对 Memory 的高级封装, 可以使用 MemoryDef 从上下文中获取.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends \ArrayAccess, ArrayAndJsonAble, ClonerInstance, \IteratorAggregate
{
    public function getId() : string;

    public function getName() : string;

    public function isLongTerm() : bool;

    public function isChanged() : bool;

    public function toData() : array;

    public function keys() : array;
}