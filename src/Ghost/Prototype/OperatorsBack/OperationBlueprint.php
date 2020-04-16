<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack;
use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;


/**
 * Ghost 的多轮对话管理是一个比较复杂的状态机.
 * 会有 Stage A 到 B 到 C 的相互调用.
 * 正常代码的写法会导致 PHP 的栈太深, 不利于问题排查.
 *
 * 因此把 PHP 的栈在代码层面实现. 运行中的每一块逻辑会拆成一个 Operator
 * Operator 相当于面向过程的一段代码. Operator 链可实现复杂的调度逻辑.
 *
 * 然而 Operator 链并不直观, 容易变得难以理解.
 * 因此在这里用伪代码的方式记录面向过程的运行流程, 方便整理思路.
 *
 *
 *
 * 在这里用伪代码来设计全部的流程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OperationBlueprint
{
    /**
     * @var Conversation
     */
    protected $convo;

    public function __construct(Conversation $conversation)
    {
        $this->convo = $conversation;
    }

    /*---------- 线索1 会话开启运行 ----------*/

    public function processStart()
    {
        $message = $this->convo->ghostInput->getMessage();

        // 说明是从别的对话投递过来的异步任务.
        if ($message instanceof YieldMsg) {
            return $this->processFromYield();
        }

        // 说明是从别的对话返回过来的异步任务.
        if ($message instanceof RetainMsg) {
            return $this->processToRetain();
        }

        // 正常流程
        return $this->processCheckBlock();
    }

    protected function processCheckBlock()
    {
        // check block

        return $this->processOnHear();
    }

    protected function processOnHear()
    {

        return $this->comprehendPipes()

            // 检查是否命中了 Stage 路由
            ?? $this->stagesRouting()

            // 检查是否命中了 Context 路由
            ?? $this->contextRouting()

            // 让当前 Stage 认真听.
            ?? $this->stageOnHeed();
    }

    protected function processFromYield()
    {
        if ($this->tryToBlock()) {
            return $this->processOnWake();
        }

        return $this->processEndQuiet();
    }

    protected function processToRetain()
    {
        if ($this->tryToBlock()) {
            return $this->processOnWake();
        }

        return $this->processEndQuiet();
    }

    /*---------- 线索2  ----------*/

}