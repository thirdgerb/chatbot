<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Intend;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Prototype\Stage\IOnIntendStage;


/**
 * 尝试进入另一个 Context. 有时候是同一个.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntendToContext implements Operator
{

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Node
     */
    protected $fromNode;

    /**
     * @var Node
     */
    protected $intending;

    /**
     * IntendToStage constructor.
     * @param StageDef $stageDef
     * @param Node $fromNode
     * @param Node $intending
     */
    public function __construct(
        StageDef $stageDef,
        Node $fromNode,
        Node $intending
    )
    {
        $this->stageDef = $stageDef;
        $this->fromNode = $fromNode;
        $this->intending = $intending;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        // 以 form 为状态, 创建 Stage 对象
        $intendStage = new IOnIntendStage(
            $conversation,
            $this->stageDef,
            $this->fromNode,
            $this->intending
        );

        // 交给 To 的 StageDef 去处理.
        $stageDef = $this->intending->findStageDef($conversation);
        return $stageDef->onIntend($intendStage);
    }



}