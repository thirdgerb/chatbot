<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Hear;

use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Prototype\Operators\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RoutingPipes extends AbsOperator
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @var StageDef
     */
    protected $stageDef;

    public function invoke(Conversation $conversation): ? Operator
    {

        $middleware = $this->stageDef->comprehendPipes();
        if (!empty($middleware)) {
            $conversation = $conversation->goThroughPipes($middleware);
        }

        // to stage route
    }


}