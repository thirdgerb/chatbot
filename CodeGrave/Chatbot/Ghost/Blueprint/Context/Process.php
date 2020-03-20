<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Context;


/**
 * 对话进程
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read string $belongsTo 进程从属的 ID. 从属对象可以是 ChatId, 或一个父进程
 *
 * @property-read string $id  进程ID. 缓存进程时依赖的数据.
 */
interface Process
{
    public function aliveThread() : Thread;

    public function aliveStage() : Stage;


    /*------- status -------*/


    /*------- methods -------*/

    /**
     * @param Thread|null $thread
     */
    public function sleepTo(? Thread $thread) : void;


    /*------- expire -------*/

    /**
     * 进程的过期时间
     * @return int timestamp
     */
    public function expiredAt() : int;
}