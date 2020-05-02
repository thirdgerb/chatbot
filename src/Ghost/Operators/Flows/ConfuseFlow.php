<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Flows;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\Ending\ReallyConfused;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\OnConfuse\TryGC;
use Commune\Ghost\Operators\OnConfuse\TryWake;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ConfuseFlow extends FlowOperator
{
    protected $domino = [
        TryWake::class,
        TryGC::class,
        ReallyConfused::class,
    ];

    protected function doInvoke(Cloner $cloner): ? Operator
    {
        // 推倒骨牌!
        return null;
    }
}