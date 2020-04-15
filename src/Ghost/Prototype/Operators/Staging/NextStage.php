<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Staging;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Fallback\FulfillCurrent;


/**
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
        $node = $runtime->getProcess()->aliveThread()->currentNode();

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