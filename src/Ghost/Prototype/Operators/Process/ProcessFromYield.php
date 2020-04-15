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
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Contexts\YieldContext;
use Commune\Ghost\Prototype\Operators\Redirect\TryBlock;
use Commune\Ghost\Prototype\Stage\IIntendStage;
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
     * @param Process $process
     * @param YieldMsg $yieldMsg
     */
    public function __construct(Process $process, YieldMsg $yieldMsg)
    {
        $this->yieldMsg = $yieldMsg;
        parent::__construct($process);
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        $context = new YieldContext($this->yieldMsg);

        return new TryBlock($this->process, $context);
    }


}