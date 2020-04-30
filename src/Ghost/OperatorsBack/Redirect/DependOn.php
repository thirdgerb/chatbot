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

    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();

        $node = $this->on->toNewNode();
        $process->aliveThread()->pushNode($node);

        $stageDef = $node->findStageDef($cloner);

        return new ToActivateStage($stageDef, $node);
    }


}