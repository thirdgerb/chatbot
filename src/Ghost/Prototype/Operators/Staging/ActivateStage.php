<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Staging;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Stage\IActivateStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ActivateStage extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(StageDef $stageDef, Context $context)
    {
        $this->stageDef = $stageDef;
        $this->context = $context;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $activateStage = new IActivateStage(
            $conversation,
            $this->stageDef,
            $this->context
        );

        return $this->stageDef->onActivate($activateStage);
    }


}