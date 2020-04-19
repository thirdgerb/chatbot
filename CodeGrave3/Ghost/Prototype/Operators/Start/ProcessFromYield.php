<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Start;

use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Contexts\YieldContext;
use Commune\Ghost\Prototype\Operators\Current\HearStage;
use Commune\Ghost\Prototype\Operators\Current\RetainStage;
use Commune\Ghost\Prototype\Operators\Current\WakeStage;
use Commune\Ghost\Prototype\Operators\Pipe\PipeOperator;


/**
 * 处理其它位置投递过来的 Yield 消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessFromYield implements Operator
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
        $process->blockThread($thread);

        // 挑战当前的 Thread
        $pop = $process->challengeAliveThread();

        // 抢占成功, 先 Wake 然后再 hear
        if (isset($pop)) {
            // 强制睡掉当前的 Thread.
            $process->addSleepingThread($pop);
            return new RetainStage();
        }

        // 抢占失败, 直接退出所有流程.
        return null;
    }



}