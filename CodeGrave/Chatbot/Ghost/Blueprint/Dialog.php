<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Commune\Chatbot\Ghost\Blueprint\Context\Process;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Process $process 当前对话所处进程
 * @property-read Context $context 当前对话所处语境
 */
interface Dialog
{

    /*------- inside context redirection -------*/

    /**
     * 重复当前 Stage
     * @return Redirector
     */
    public function repeat() : Redirector;

    /**
     * 回到当前 Context 的起点
     * @return Redirector
     */
    public function restart() : Redirector;

    /**
     * 重置当前 Context, 包括重置 Context 的数据
     * @return Redirector
     */
    public function reset() : Redirector;

    /**
     * 切换 Stage
     * @param string $stageName
     * @return Redirector
     */
    public function goStage(string $stageName) : Redirector;

    /**
     * 依次进入若干个 Stage
     * @param string[] $stageNames
     * @return Redirector
     */
    public function goStagePipes(array $stageNames) : Redirector;

    /**
     * 重置当前的 Stage pipes
     */
    public function resetStagePipes() : void;

    /**
     * 进入下一个 Stage, 不存在的话则执行 fulfill
     * @return Redirector
     */
    public function next() : Redirector;

    /*------- go to other context -------*/

    /**
     * 返回 Process 的起点
     * @return Redirector
     */
    public function home() : Redirector;

    /**
     * Sleep 当前的 Thread, 并进入另一个 Thread
     * 如果目标 Thread 不存在, 而当前 Thread 又是唯一的, 则退出会话
     * @return Redirector
     */
    public function sleepTo() : Redirector;

    /**
     * 依赖另一个 Context, 目标 Context 结束后会回调当前 Stage
     * @return Redirector
     */
    public function dependOn() : Redirector;

    /*----- yield -----*/

    /**
     * 中断当前任务, 并等待一个回调, 然后继续.
     * @return Redirector
     */
    public function yieldTo() : Redirector;

    /*----- backward -----*/

    /**
     * 等待用户响应
     * @return Redirector
     */
    public function wait() : Redirector;

    /**
     * 表示无法处理用户的响应.
     * @return Redirector
     */
    public function confuse() : Redirector;


    /*----- backward -----*/

    /**
     * 重置为上一轮对话的终态
     * @return Redirector
     */
    public function rewind() : Redirector;

    /**
     * 返回到上上轮对话的终态
     * @return Redirector
     */
    public function back() : Redirector;


    /*------- exiting -------*/

    /**
     * 退出会话
     * @return Redirector
     */
    public function quit() : Redirector;

    /**
     * 退出当前的 Thread, 可以被 intended stage 拦截.
     * @return Redirector
     */
    public function cancel() : Redirector;

    /**
     * 结束当前的 Context, 回调 intended stage
     * @return Redirector
     */
    public function fulfill() : Redirector;


}