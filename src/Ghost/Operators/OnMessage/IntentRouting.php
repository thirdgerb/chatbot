<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnMessage;

use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;
use Commune\Message\Blueprint\Internal\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntentRouting extends AbsOperator
{
    /**
     * @var InputMsg
     */
    protected $incoming;

    /**
     * @var StageDef
     */
    protected $stageDef;

    public function invoke(): ? Operator
    {
        $intentMessage = $this->match(
            $this->incoming->comprehended,
            $this->stageDef->routingIntents()
        );

        if (!empty($intentMessage)) {
            // return Intending
        }

        // 如果没有匹配, 尝试检查子会话
        $childId = $this->runtime->getProcess()->childId;
        if ($childId) {
            // 进入子进程.
            // return new start Process
        }

        // 如果没有子会话, 检查自己的 Heard
        // return heard stage
    }


}