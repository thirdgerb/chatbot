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
class RestartContext implements Operator
{

    /**
     * @var Node
     */
    protected $node;

    /**
     * ResetContext constructor.
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $this->node->reset();
        $contextDef = $this->node->findContextDef($conversation);
        $stageDef = $contextDef->getInitialStageDef();

        return new ActivateStage(
            $stageDef,
            $this->node
        );
    }

}