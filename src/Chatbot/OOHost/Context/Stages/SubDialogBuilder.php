<?php

/**
 * Class SubDialogBuilder
 * @package Commune\Chatbot\OOHost\Context\Stages
 */

namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * stage 应对子会话功能的 builder
 * 调用 end() 返回 navigator
 */
interface SubDialogBuilder
{

    /**
     * 子会话启动的时候执行的逻辑.
     * 如果不传入的话, 则会 repeat root context
     *
     * @param callable $callable
     * @return SubDialogBuilder
     */
    public function onInit(callable  $callable) : SubDialogBuilder;

    /**
     * 进入子会话之前, 由父会话做的拦截.
     *
     * @param callable $callable
     * @return SubDialogBuilder
     */
    public function onBefore(callable $callable) : SubDialogBuilder;

    /**
     * 子会话退出时, 父会话的反应.
     *
     * @param callable $callable
     * @return SubDialogBuilder
     */
    public function onQuit(callable $callable) : SubDialogBuilder;

    /**
     * 子会话没有处理时, 父会话的反应.
     * @param callable $callable
     * @return SubDialogBuilder
     */
    public function onMiss(callable $callable) : SubDialogBuilder;

    /**
     * 子会话 wait时, 父会话的反应.
     * @param callable $callable
     * @return SubDialogBuilder
     */
    public function onWait(callable $callable) : SubDialogBuilder;

    public function end() : Navigator;

}