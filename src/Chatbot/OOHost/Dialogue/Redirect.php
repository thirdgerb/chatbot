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
     * @param $dependency
     * @param array|null $certainStages 可以指定运行的 stage. 通常不需要设置.
     * @return Navigator
     */
    public function dependOn($dependency, array $certainStages = null) : Navigator;

    /**
     * 忘记当前的thread, 进入一个新的thread
     *
     * @param Context|string $to
     * @param string $level
     * @return Navigator
     */
    public function replaceTo($to, string $level = Redirect::THREAD_LEVEL) : Navigator;

    /**
     * @param Context|string $to
     * @return Navigator
     */
    public function sleepTo($to = null) : Navigator;

    /**
     * @param Context|string|null $to
     * @return Navigator
     */
    public function yieldTo($to = null) : Navigator;

    /**
     * @return Navigator
     */
    public function home() : Navigator;

}