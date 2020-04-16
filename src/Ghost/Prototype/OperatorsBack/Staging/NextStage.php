<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Staging;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;
use Commune\Ghost\Prototype\OperatorsBack\Fallback\FulfillCurrent;


/**
 * 当前 Stage 调用 next 方法之后的流程. 将多个 Stage 作为前进管道
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NextStage extends AbsOperator
{
    /**
     * @var array
     */
    protected $next;

    /**
     * NextStage constructor.
     * @param array $next
     */
    public function __construct(array $next)
    {
        $this->next = $next;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $runtime = $conversation->runtime;
        $node = $runtime->getCurrentProcess()->aliveThread()->currentNode();

        if (!empty($this->next)) {
            $node->pushStack($this->next);
        }

        // 尝试进入下一个节点, 并使之运行.
        if ($node->next()) {

            $stageDef = $node->findStageDef($conversation);
            $context = $node->findContext($conversation);
            return new ActivateStage($stageDef, $context);
        }

        return new FulfillCurrent();
    }


}