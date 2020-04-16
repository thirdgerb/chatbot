<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Staging;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Prototype\Stage\IIntendStage;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;


/**
 * 因为命中了意图, 要从同一个 Context 下的 A Stage 进入到 B Stage 时.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntendToStage extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $fromStage;

    /**
     * @var Node
     */
    protected $stageNode;

    /**
     * @var StageDef
     */
    protected $intendingStage;

    /**
     * IntendToStage constructor.
     * @param StageDef $fromStage
     * @param Node $node
     * @param StageDef $intendingStage
     */
    public function __construct(
        StageDef $fromStage,
        Node $node,
        StageDef $intendingStage
    )
    {
        $this->fromStage = $fromStage;
        $this->stageNode = $node;
        $this->intendingStage = $intendingStage;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        // 以 form 为状态, 创建 Stage 对象
        $intendStage = new IIntendStage(
            $conversation,
            $this->fromStage,
            $this->stageNode,
            $this->stageNode
        );

        return $this->intendingStage->onIntend($intendStage);
    }


}