<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Staging;

use Commune\Blueprint\Ghost\Context\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\OperatorsBack\Events\ToActivateStage;

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

    public function invoke(Cloner $cloner): ? Operator
    {
        // 重置所有数据.
        $this->context->reset([]);
        $this->node->reset();
        $stageDef = $this->context->getDef()->getInitialStageDef();

        return new ToActivateStage(
            $stageDef,
            $this->node
        );
    }


}