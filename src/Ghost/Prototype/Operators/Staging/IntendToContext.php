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
class IntendToContext extends AbsOperator
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
     * @var Context
     */
    protected $intending;

    /**
     * IntendToStage constructor.
     * @param StageDef $fromStage
     * @param Context $fromContext
     * @param Context $intending
     */
    public function __construct(
        StageDef $fromStage,
        Context $fromContext,
        Context $intending
    )
    {
        $this->fromStage = $fromStage;
        $this->fromContext = $fromContext;
        $this->intending = $intending;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        // 以 form 为状态, 创建 Stage 对象
        $intendStage = new IIntendStage(
            $conversation,
            $this->fromStage,
            $this->fromContext,
            $this->intending
        );

        // 交给 To 的 StageDef 去处理.
        $stageDef = $conversation
            ->mind
            ->contextReg()
            ->getDef($this->intending->getName())
            ->getInitialStageDef();

        return $stageDef->onIntend($intendStage);
    }


}