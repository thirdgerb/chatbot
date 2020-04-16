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
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Prototype\Operators\Events\ActivateStage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ResetContext implements Operator
{

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Node
     */
    protected $node;

    /**
     * ResetContext constructor.
     * @param Context $context
     * @param Node $node
     */
    public function __construct(Context $context, Node $node)
    {
        $this->context = $context;
        $this->node = $node;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        // 重置所有数据.
        $this->context->reset([]);
        $this->node->flushStack();
        $stageDef = $this->node->findStageDef($conversation);

        return new ActivateStage(
            $stageDef,
            $this->node
        );
    }


}