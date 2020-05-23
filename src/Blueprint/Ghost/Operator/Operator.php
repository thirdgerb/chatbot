<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operator;

use Commune\Blueprint\Ghost\Dialog;

/**
 * 多轮对话逻辑算子, 链式调用.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Operator
{

    /**
     * Dialog 逻辑运行一帧.
     * @return Operator
     */
    public function tick() : Operator;


    /**
     * @return bool
     */
    public function isTicking() : bool;

    /**
     * @return bool
     */
    public function isTicked() : bool;


    /**
     * 结束 operator, 不允许继续运行.
     */
    public function ticked() : void;

    /**
     * @param callable $caller
     */
    public function pushStack(callable $caller) : void;

    /**
     * @return callable[]
     */
    public function dumpStack() : array;

    /**
     * @return string
     */
    public function getOperatorDesc() : string;

    /**
     * @return Dialog
     */
    public function getDialog() : Dialog;
}