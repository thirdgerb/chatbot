<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Speech;
use Commune\Chatbot\OOHost\Directing\Navigator;

interface OnCallbackStage extends Speech
{
    /**
     * @param callable $action
     * @return OnCallbackStage
     */
    public function interceptor(callable $action) : OnCallbackStage;

    /**
     * @return Hearing
     */
    public function hearing();

    /**
     * @param callable $action
     * @return Navigator
     */
    public function action(callable $action) : Navigator;

    /*------ navigation ------*/

    /*-------- 完成 --------*/

    /**
     * 触发下一个 stage, 没有的话调用fulfill
     * @return Navigator
     */
    public function next() : Navigator;

    /**
     * 完成当前的任务, 向前回调.
     * @return Navigator
     */
    public function fulfill() : Navigator;


    /*-------- context stage --------*/

    /**
     * 重新开始当前 context.
     * @return Navigator
     */
    public function restart() : Navigator;

    /**
     * 当前 context 进入下一个 stage
     *
     * @param string $stageName
     * @param bool $resetPipe   是否重置掉当前stage 回调的stage路径.
     * @return Navigator
     */
    public function goStage(
        string $stageName,
        bool $resetPipe = false
    ) : Navigator;

    /**
     * 重新 start 当前的 stage
     * @return Navigator
     */
    public function repeat() : Navigator;

    /**
     * @param array $stages
     * @param bool $resetPipe   是否重置掉当前stage 回调的stage路径.
     * @return Navigator
     */
    public function goStagePipes(
        array $stages,
        bool $resetPipe = false
    ) : Navigator;


    /*-------- history --------*/

    /**
     * 退回到上一个向用户提出问题的状态.
     * @return Navigator
     */
    public function backward() : Navigator;

    /**
     * 重新向用户提出上一个问题
     * 而不是重复当前stage
     * @return Navigator
     */
    public function rewind() : Navigator;

    /**
     * 明确告诉用户 miss match. 然后执行 repeat
     * @return Navigator
     */
    public function missMatch() : Navigator;

    /**
     * 用同一个stage 等待用户的下一次消息.
     * @return Navigator
     */
    public function wait() : Navigator;

    /**
     * 退出当前的 session. 下次用户进来, 会从头开始对话.
     * @return Navigator
     */
    public function quit() : Navigator;


    /*-------- 异常 --------*/

    /**
     * 机器人拒绝用户进入当前的语境.
     *
     * @return Navigator
     */
    public function reject() : Navigator;

    /**
     * 用户主动取消当前的语境.
     *
     * @return Navigator
     */
    public function cancel() : Navigator;


}