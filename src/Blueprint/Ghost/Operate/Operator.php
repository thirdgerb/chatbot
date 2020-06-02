<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;

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
     * @return Dialog
     */
    public function getDialog() : Dialog;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * 可以作为 callable 对象传给 dialog::caller
     * @return Operator
     */
    public function __invoke() : Operator;
}