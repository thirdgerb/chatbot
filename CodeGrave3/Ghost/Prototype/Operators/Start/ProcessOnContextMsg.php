<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Start;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Current\HearStage;
use Commune\Ghost\Prototype\Operators\Intend\IntendToContext;
use Commune\Message\Blueprint\ContextMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessOnContextMsg implements Operator
{
    /**
     * @var ContextMsg
     */
    protected $message;

    /**
     * ProcessOnIntent constructor.
     * @param ContextMsg $message
     */
    public function __construct(ContextMsg $message)
    {
        $this->message = $message;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $message = $this->message;

        $contextReg = $conversation->mind->contextReg();
        $contextName = $this->message->getContextName();
        $hasContext = $contextReg->hasDef($contextName);

        $def = null;
        if ($hasContext) {
            $def = $contextReg->getDef($contextName);
        }

        // 如果是一个可以命中的意图.
        if (isset($def) && $def->isPublic()) {
            $process = $conversation->runtime->getCurrentProcess();
            $from = $process->aliveThread()->currentNode();
            $to = $conversation
                ->newContext($contextName, $message->getEntities())
                ->toNewNode();

            return new IntendToContext(
                $from->findStageDef($conversation),
                $from,
                $to
            );
        }

        // 否则走 hear
        return new HearStage();
    }


}