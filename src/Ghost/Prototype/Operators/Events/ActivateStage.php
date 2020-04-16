<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Events;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Prototype\Stage\IActivateStage;


/**
 * 启动当前 Stage
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ActivateStage implements Operator
{

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Node
     */
    protected $node;

    /**
     * ActivateStage constructor.
     * @param StageDef $stageDef
     * @param Node $node
     */
    public function __construct(StageDef $stageDef, Node $node)
    {
        $this->stageDef = $stageDef;
        $this->node = $node;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        $stage = new IActivateStage(
            $conversation,
            $this->stageDef,
            $this->node
        );

        return $this->stageDef->onActivate($stage);
    }


}