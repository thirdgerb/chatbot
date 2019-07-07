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
     * 用同一个stage 等待用户的下一次消息.
     * @return Navigator
     */
    public function wait() : Navigator;



}