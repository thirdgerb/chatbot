<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Process;

use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Dialog\IHeed;
use Commune\Ghost\Prototype\Operators\AbsOperator;


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

        $context = $conversation->newContext(
            $this->retainMsg->getContextName(),
            $this->retainMsg->getEntities()
        );

        $yielding->dependOn($context);

        $blocked = $conversation->runtime->getProcess()->blockThread($yielding);

        // 没有消息就直接结束了. 等待下次回调.
        if (!$blocked) {
            return null;
        }

        // context 进行回调.
        $heed = new IHeed($conversation);
        return $heed->staging()->fulfill();
    }


}