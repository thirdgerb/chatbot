<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Staging;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Stage\IHeedStage;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageOnHeed extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Context
     */
    protected $context;

    /**
     * StageOnHeed constructor.
     * @param StageDef $stageDef
     * @param Context $context
     */
    public function __construct(StageDef $stageDef, Context $context)
    {
        $this->stageDef = $stageDef;
        $this->context = $context;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $dialog = new IHeedStage(
            $conversation,
            $this->stageDef,
            $this->context
        );

        return $this->stageDef->onHeed($dialog);
    }


}