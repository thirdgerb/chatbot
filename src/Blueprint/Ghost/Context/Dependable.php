<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Cloner\ClonerInstance;

/**
 * 定义一个语境中的可以依赖对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Dependable extends ClonerInstance
{
    /**
     * 该对象是否已经完备.
     * 不完备的话则应该跳转.
     * @return bool
     */
    public function isFulfilled() : bool;

    /**
     * 如果不完备, 可以进入该 Ucl 去完备它.
     * @return Ucl
     */
    public function toFulfillUcl() : Ucl;

}