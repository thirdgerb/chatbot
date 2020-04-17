<?php


/**
 * Class StageOnHeed
 * @package Commune\Ghost\Prototype\Operators\Staging
 */

namespace Commune\Ghost\Prototype\Operators\Staging;


use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Prototype\Stage\IOnHeedStage;

class StageOnHeed implements Operator
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
     * StageOnHeed constructor.
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
        $heed = new IOnHeedStage(
            $conversation,
            $this->stageDef,
            $this->node
        );
        return $this->stageDef->onHeed($heed);
    }


}