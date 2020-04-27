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
 * 记忆单元. 用数组的方式存取信息.
 * 允许存取的信息只有三种, is_scalar(), 纯 array, Stub 类型对象.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends
    ArrayAccess, // 数组式的调用
    ArrayAndJsonAble, // 可转化为数组.
    Cachable, // 可以缓存
    Savable, // 可以长期存储, 重点看 isLongTerm
    Memorable // Recollection 实例可以放到另一个 Recollection 实例中做参数.
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

    /**
     * 数据更改过.
     * @return bool
     */
    public function isChanged(): bool;


}