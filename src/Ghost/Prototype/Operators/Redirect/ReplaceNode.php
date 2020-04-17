<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Redirect;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Events\ActivateStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ReplaceNode implements Operator
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * ReplaceNode constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $thread = $conversation->runtime->getCurrentProcess()->aliveThread();
        $node = $this->context->toNewNode();

        $thread->replaceNode($node);
        $stageDef = $node->findStageDef($conversation);
        return new ActivateStage($stageDef, $node);
    }


}