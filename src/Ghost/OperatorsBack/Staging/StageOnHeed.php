<?php


/**
 * Class StageOnHeed
 * @package Commune\Ghost\Operators\Staging
 */

namespace Commune\Ghost\OperatorsBack\Staging;


use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\Stage\IOnHeedStage;

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


    public function invoke(Cloner $cloner): ? Operator
    {
        $heed = new IOnHeedStage(
            $cloner,
            $this->stageDef,
            $this->node
        );
        return $this->stageDef->onHeed($heed);
    }


}