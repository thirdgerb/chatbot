<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnMessage;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;

/**
 * 启动一个进程
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StartProcess extends AbsOperator
{
    public function invoke(): ? Operator
    {
        // 第一步， 检查路由。
        $route = $this->runtime->getRoute();
        if (!$this->mind->hasContextDef()) {
            // todo failue
            // return failure ???
        }

        $contextDef = $this->mind->getContextDef($route->contextName);
        $stageName = $route->stageName;
        if (!$contextDef->hasStage($stageName)) {
            // todo failure
        }
        $stageDef = $contextDef->getStage($stageName);
        return new StageRouting($this->session, $stageDef);

    }


}