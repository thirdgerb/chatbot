<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Retrace;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Thread;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Stage\IRetraceStage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FulfillRetrace extends AbsOperator
{

    /**
     * @var Thread
     */
    protected $thread;

    /**
     * @var Context
     */
    protected $from;

    public function __construct(
        Thread $thread,
        Context $from
    )
    {
        $this->thread = $thread;
        $this->from = $from;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $currentNode = $this->thread->currentNode();
        $stageDef = $currentNode->findStageDef($conversation);
        $context = $currentNode->findContext($conversation);

        $fulfillStage = new IRetraceStage(
            $conversation,
            $stageDef,
            $context,
            $this->from
        );

        return $stageDef->onFulfill($fulfillStage);
    }


}