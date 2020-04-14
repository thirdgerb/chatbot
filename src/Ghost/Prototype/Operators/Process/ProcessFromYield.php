<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Process;

use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Contexts\YieldContext;
use Commune\Ghost\Prototype\Dialog\IIntend;
use Commune\Ghost\Prototype\Operators\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessFromYield extends AbsOperator
{
    /**
     * @var YieldMsg
     */
    protected $yieldMsg;

    /**
     * ProcessFromYield constructor.
     * @param YieldMsg $yieldMsg
     */
    public function __construct(YieldMsg $yieldMsg)
    {
        $this->yieldMsg = $yieldMsg;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        $context = new YieldContext($this->yieldMsg);
        $dialog = new IIntend($conversation, $context);
        return $dialog->stageEvent()->onActivate();
    }


}