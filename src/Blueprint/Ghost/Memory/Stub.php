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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;


/**
 * 可以作为参数存储到 Recollection 中的数据.
 * 是允许序列化的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Stub extends BabelSerializable, ArrayAndJsonAble
{
    public function toMemorable(Cloner $cloner) : ? Memorable;
}