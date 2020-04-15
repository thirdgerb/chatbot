<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Stage;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\StageChangeState;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Blueprint\Speak\Speaker;


/**
 * 多轮对话管理器. 在多轮对话逻辑中, 一切都通过它来管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Conversation $conversation
 * @property-read StageDef $def
 * @property-read Context $self
 */
interface Stage
{
    /**
     * 用依赖注入的方式调用一个 callable.
     * 与laravel 的区别在于, $parameters 允许用 interface => $instance 的方式注入临时依赖.
     *
     * @param callable $caller
     * @param array $parameters
     * @return mixed
     */
    public function call(callable $caller, array $parameters = []);

    /**
     * 运行一个逻辑, 然后得到一个 Operator 算子.
     *
     * @param callable $action
     * @return Operator|null
     */
    public function operate(callable $action) : ? Operator;

    /**
     * 当前 stage 的状态管理.
     * @return StageChangeState
     */
    public function change() : StageChangeState;

    /**
     * @return Speaker
     */
    public function speak() : Speaker;
}