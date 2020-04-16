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

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Events\ActivateStage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NextStages implements Operator
{

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var string[]
     */
    protected $next;

    /**
     * NextStages constructor.
     * @param StageDef $stageDef
     * @param string[] $next
     */
    public function __construct(StageDef $stageDef, array $next)
    {
        $this->stageDef = $stageDef;
        $this->next = $next;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        $node = $conversation->runtime->getCurrentProcess()->aliveThread()->currentNode();

        // 入栈新的路径.
        if (!empty($this->next)) {
            $node->pushStack($this->next);
        }

        // 下一个节点存在.
        if ($node->next()) {
            $stageDef = $node->findStageDef($conversation);
            return new ActivateStage(
                $stageDef,
                $node
            );
        }

        // todo
    }


}