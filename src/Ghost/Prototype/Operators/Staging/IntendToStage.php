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
use Commune\Ghost\Prototype\Stage\IIntendStage;
use Commune\Ghost\Prototype\Operators\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntendToStage extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $fromStage;

    /**
     * @var Context
     */
    protected $fromContext;

    /**
     * @var StageDef
     */
    protected $intendingStage;

    /**
     * IntendToStage constructor.
     * @param StageDef $fromStage
     * @param Context $fromContext
     * @param StageDef $intendingStage
     */
    public function __construct(
        StageDef $fromStage,
        Context $fromContext,
        StageDef $intendingStage
    )
    {
        $this->fromStage = $fromStage;
        $this->fromContext = $fromContext;
        $this->intendingStage = $intendingStage;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        // 以 form 为状态, 创建 Stage 对象
        $intendStage = new IIntendStage(
            $conversation,
            $this->fromStage,
            $this->fromContext,
            $this->fromContext
        );

        return $this->intendingStage->onIntend($intendStage);
    }


}