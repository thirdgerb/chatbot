<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Redirect;

use Commune\Blueprint\Ghost\Context\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\OperatorsBack\Events\ToActivateStage;


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

    public function invoke(Cloner $cloner): ? Operator
    {
        $thread = $cloner->runtime->getCurrentProcess()->aliveThread();
        $node = $this->context->toNewNode();

        $thread->replaceNode($node);
        $stageDef = $node->findStageDef($cloner);
        return new ToActivateStage($stageDef, $node);
    }


}