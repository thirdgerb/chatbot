<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;


/**
 * 当一个 context 要退出时, 可以触发以下方法,
 * 决定是直接退出, 或是加一些回复, 或是跳转到别的context
 *
 * @property-read AbsContext $self
 * @property-read Dialog $dialog
 * @property null|Navigator $navigator
 */
interface Exiting
{

    /**
     * 完成当前context, 正常退出前
     * 拦截自己的fulfill
     * 不处理navigator
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onFulfill(callable $interceptor) : Exiting;


    /**
     * 拦截上游的backward
     * 不包括自身. 只会在回调的时候触发.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onBackward(callable $interceptor) : Exiting;


    /**
     * 拦截quit. 包括自身.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onQuit(callable $interceptor) : Exiting;

    /**
     * 拦截reject. 包括自身.
     *
     * context 拒绝了用户的访问, 退出之前.
     * 比如 context 要求用户登录, 可以在这儿让用户进入登录的 context
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onReject(callable $interceptor) : Exiting;

    /**
     * 拦截cancel. 包括自身.
     * 用 $dialog->cancel(true) 可以继续cancel
     *
     * 用户拒绝了context 的多轮对话.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onCancel(callable $interceptor) : Exiting;

    /**
     * 拦截failure. 包括自身.
     * 下游对话出错时.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onFail(callable $interceptor) : Exiting;


}