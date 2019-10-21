<?php


namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 从dialog 中手动跳转到其它context
 * 不建议使用. 最好定义 stage 方法, 通过checkpoint 来跳转.
 */
interface Redirect
{
    const PROCESS_LEVEL = 'process';
    const THREAD_LEVEL = 'thread';
    const NODE_LEVEL = 'node';

    /**
     * 表示依赖一个 context
     *
     * @param Context|string $dependency 目标context
     * @param array|null $certainStages 可以指定运行的 stage. 通常不需要设置.
     * @return Navigator
     */
    public function dependOn($dependency, array $certainStages = null) : Navigator;

    /**
     * 用一个新的 context 替换掉当前的 node, 或者thread, 或者刷新 process
     *
     * @param Context|string $to
     * @param string $level 有三种级别, Redirect::THREAD_LEVEL, node level, process_level
     * @param string|null $resetStage
     * @return Navigator
     */
    public function replaceTo(
        $to,
        string $level = Redirect::THREAD_LEVEL,
        string $resetStage = null
    ) : Navigator;

    /**
     * @param Context|string $to
     * @return Navigator
     */
    public function sleepTo($to = null) : Navigator;

    ///** 目前未完全实现. */
    // * @param Context|string|null $to
    // * @return Navigator
    // */
    //public function yieldTo($to = null) : Navigator;

    /**
     * @return Navigator
     */
    public function home() : Navigator;

}