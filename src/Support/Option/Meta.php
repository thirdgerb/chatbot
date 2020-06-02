<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Option;


/**
 * Option 实现的元数据, 通过 Meta 可以获得另一个 Wrapper 对象.
 *
 * 这样就避免了强类型的配置数组中, 同一个数组可能对应多种 Option, 导致无法预定义关联关系.
 * 现在可以统一定义 Meta 对象, 通过 Meta 再获取 Wrapper 对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Meta extends Option
{

    public function toWrapper() : Wrapper;

}