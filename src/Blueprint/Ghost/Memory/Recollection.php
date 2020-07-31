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
 * 它与 Memory 对象相比, 主要多了:
 * 1. getName() 方法.
 * 2. 实现了 ClonerInstance
 *
 * 这意味着 Recollection 是可以定义, 可以生成的.
 * 而 Memory 只是对数据的单纯封装.
 *
 * 为何要做这样的拆分呢?
 *
 * 1. 因为 Memory 对象对 Runtime 类负责, 是单纯的存取对象.
 * 它不包含产生 Memory 的过程. 而 Recollection 可以包含过程 (用name 获取).
 *
 * 2. Memory 对象是单纯的数据, 而 Recollection 对象在存储中, 会将自身转化为 Stub.
 * 从而可以关联查找.
 *
 * Memory 和 Recollection 的关系类似于 纯数据和 ORM 的关系.
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