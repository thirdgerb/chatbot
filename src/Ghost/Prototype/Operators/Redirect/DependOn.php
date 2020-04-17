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
class DependOn implements Operator
{
    /**
     * @var Context
     */
    protected $on;

    /**
     * DependOn constructor.
     * @param Context $on
     */
    public function __construct(Context $on)
    {
        $this->on = $on;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();

        $node = $this->on->toNewNode();
        $process->aliveThread()->pushNode($node);

        $stageDef = $node->findStageDef($conversation);

        return new ToActivateStage($stageDef, $node);
    }


}