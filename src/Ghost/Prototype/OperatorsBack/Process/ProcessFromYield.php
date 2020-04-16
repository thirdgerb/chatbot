<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Process;

use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Contexts\YieldContext;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;
use Commune\Ghost\Prototype\OperatorsBack\Breakpoint\EndOperation;
use Commune\Ghost\Prototype\OperatorsBack\Staging\WakeStage;

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


    /**
     * @param Conversation $conversation
     * @return Operator|null
     */
    public function invoke(Conversation $conversation): ? Operator
    {
        // 获取 process
        $process = $conversation->runtime->getCurrentProcess();

        // 创建一个 YieldContext, 负责在任务结束时返回投递
        $context = new YieldContext($this->yieldMsg);
        $context = $context->toInstance($conversation);

        // 尝试 block 新的 Thread
        $thread = $context->toNewNode()->toThread();
        $success = $process->challengeAliveThread($thread);

        // 抢占成功, 直接 Wake
        if ($success) {
            $node = $process->aliveThread()->currentNode();
            $stageDef = $node->findStageDef($conversation);
            return new WakeStage($stageDef, $node);
        }

        // 抢占失败, 等待未来自动
        return new EndOperation();
    }


}