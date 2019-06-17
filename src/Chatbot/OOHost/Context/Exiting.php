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
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onFulfill(callable $interceptor) : Exiting;


    /**
     * @param callable $interceptor
     * @return Exiting
     */
    public function onQuit(callable $interceptor) : Exiting;

    /**
     * context 拒绝了用户的访问, 退出之前.
     * 比如 context 要求用户登录, 可以在这儿让用户进入登录的 context
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onReject(callable $interceptor) : Exiting;

    /**
     * 用户拒绝了context 的多轮对话.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onCancel(callable $interceptor) : Exiting;

    /**
     * 下游对话出错时.
     *
     * @param callable $interceptor
     * @return Exiting
     */
    public function onFail(callable $interceptor) : Exiting;

    /**
     * @param callable $interceptor
     * @return Exiting
     */
    public function onBackward(callable $interceptor) : Exiting;


}