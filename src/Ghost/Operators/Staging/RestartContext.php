<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Staging;

use Commune\Blueprint\Ghost\Context\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\Operators\Events\ToActivateStage;


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

    public function invoke(Cloner $cloner): ? Operator
    {
        $this->node->reset();
        $contextDef = $this->node->findContextDef($cloner);
        $stageDef = $contextDef->getInitialStageDef();

        return new ToActivateStage(
            $stageDef,
            $this->node
        );
    }

}