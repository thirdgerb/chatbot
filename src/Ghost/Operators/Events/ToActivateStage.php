<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Events;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\Stage\IOnActivateStage;


/**
 * 启动当前 Stage
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ToActivateStage implements Operator
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


    public function invoke(Cloner $cloner): ? Operator
    {
        $stage = new IOnActivateStage(
            $cloner,
            $this->stageDef,
            $this->node
        );

        return $this->stageDef->onActivate($stage);
    }


}