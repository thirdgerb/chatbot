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

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Current\HearStage;
use Commune\Ghost\Prototype\Operators\Current\RetainStage;
use Commune\Ghost\Prototype\Operators\Pipe\PipeOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessCheckBlock implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();

        if (!$process->hasBlocking()) {
            return new HearStage();
        }

        // 如果挑战成功.
        $popped = $process->challengeAliveThread();
        if (isset($popped)) {
            $process->addSleepingThread($popped);

            // 先执行 retain, 然后再去 hear
            // 要避免问题的处理逻辑有一些偏差.
            return new PipeOperator(
                new RetainStage(),
                new HearStage()
            );
        }

        // 正常开始听取消息.
        return new HearStage();
    }


}