<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\OnMessage;

use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Session\GhtSession;
use Commune\Ghost\Prototype\Operators\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageRouting extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $stageDef;

    public function invoke(): ? Operator
    {
        $stages = $this->stageDef->routingStages();
        $contextDef = $this->stageDef->getContextDef();

        foreach ($stages as $name) {
            $stageDef = $contextDef->getStage($name);
            $intendDef = $stageDef->getRoutingDef();
            // 如果匹配成功
            if ($intendDef->validate($this->session)) {

                // return heard stage
            }
        }

        // return IntentRouting
    }


}