<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeech;
use Commune\Chatbot\OOHost\Directing\Navigator;

interface OnStartStage extends DialogSpeech
{
    /**
     * @param callable|Interceptor $interceptor
     * @return OnStartStage
     */
    public function interceptor(callable $interceptor) : OnStartStage;

    /*--------- 直接跳转 ---------*/

    /**
     * @param string $name
     * @param bool $resetPipes
     * @return Navigator
     */
    public function goStage(
        string $name,
        bool $resetPipes = false
    ) : Navigator;

    /**
     * @param array $pipes
     * @param bool $resetPipes
     * @return Navigator
     */
    public function goStagePipes(
        array $pipes,
        bool $resetPipes = false
    ) : Navigator;


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

    /**
     * 忘记当前的thread, 进入一个新的thread
     *
     * @param Context|string $to
     * @param string $level
     * @return Navigator
     */
    public function replaceTo($to, string $level = Redirect::THREAD_LEVEL) : Navigator;

    /**
     * 和interceptor 不一样, 必须返回 navigator
     * @param callable $action
     * @return Navigator
     */
    public function action(callable $action) : Navigator;


    /*--------- callback ---------*/

    /**
     * @param Context|string $to
     * @return OnCallbackStage
     */
    public function dependOn($to) : OnCallbackStage;

    /**
     * @param Context|string $to
     * @return OnCallbackStage
     */
    public function yieldTo($to) : OnCallbackStage;


    /**
     * @return OnCallbackStage
     */
    public function wait() : OnCallbackStage;

    /*--------- 返回 stage ---------*/

    /**
     * 回到stage. 做了一些没有副作用的操作.
     * @return Stage
     */
    public function toStage() : Stage;
}