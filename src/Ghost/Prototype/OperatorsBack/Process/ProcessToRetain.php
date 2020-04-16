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

use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\Stage\IHeedStage;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessToRetain extends AbsOperator
{

    /**
     * @var RetainMsg
     */
    protected $retainMsg;

    /**
     * ProcessToRetain constructor.
     * @param RetainMsg $retainMsg
     */
    public function __construct(RetainMsg $retainMsg)
    {
        $this->retainMsg = $retainMsg;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $threadId = $this->retainMsg->getThreadId();
        $yielding = $conversation->runtime->popYielding($threadId);

        // 如果回调的目标 Thread 找不到了, 则放弃掉.
        // yield 可以设置一个等待时间.
        if (!isset($yielding)) {
            // play dumb
            // todo 记录日志
            $conversation->noState();
            return null;
        }

        $yielding->pushNode($context->toNewNode());

        $blocked = $conversation->runtime->getCurrentProcess()->blockThread($yielding);

        // 没有消息就直接结束了. 等待下次回调.
        if (!$blocked) {
            return null;
        }

        $heed = new IHeedStage($conversation);

        // fulfill 的话执行完成逻辑.
        return $heed->fallback()->fulfill();
    }


}