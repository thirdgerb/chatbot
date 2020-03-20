<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Router;

use Commune\Ghost\Blueprint\Redirector;

/**
 * 当前 Thread 有关的操作.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OnThread
{

    /**
     * Sleep 当前的 Thread, 并进入另一个 Thread
     * 如果目标 Thread 不存在, 而当前 Thread 又是唯一的, 则退出会话
     *
     *
     * 回调: StageDef::onWoke
     *
     * @return Redirector
     */
    public function sleepTo() : Redirector;

    /**
     * 调用另一个 Context, 目标 Context 结束后会回调当前 Stage
     *
     * 回调: StageDef::onReturn
     *
     * @return Redirector
     */
    public function call() : Redirector;


    /**
     * 将当前 Thread 从 Process 中移除,等待回调.
     *
     * 回调 StageDef::onCallback
     *
     * @return Redirector
     */
    public function yieldTo() : Redirector;
}