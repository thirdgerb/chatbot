<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id  Thread 的唯一ID, 由 Root 决定
 * @property-read int $priority 当前 Thread 的优先级
 * @property-read int $depth Thread 的深度
 *
 */
interface Thread extends ArrayAndJsonAble
{

    public function dependOn(Context $context) : void;


    /*--------- gc ---------*/

    public function setGc(int $turns);

    /**
     * 尝试回收 Thread
     * @return bool
     */
    public function gc() : bool;

}