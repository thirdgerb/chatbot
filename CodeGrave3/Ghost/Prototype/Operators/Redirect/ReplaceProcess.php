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
use Commune\Ghost\Prototype\Operators\Events\ToActivateStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ReplaceProcess implements Operator
{
    /**
     * @var Context|null
     */
    protected $context;

    /**
     * ReplaceProcess constructor.
     * @param Context|null $context
     */
    public function __construct(Context $context = null)
    {
        $this->context = $context;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();
        $process->home($this->context);

        $node = $process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);

        return new ToActivateStage($stageDef, $node);
    }


}