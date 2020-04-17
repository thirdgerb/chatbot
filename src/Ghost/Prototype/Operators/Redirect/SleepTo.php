<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Redirect;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Current\WakeStage;
use Commune\Ghost\Prototype\Operators\End\QuitSession;
use Commune\Ghost\Prototype\Runtime\IThread;


/**
 * 将当前的 Thread 推入睡眠状态.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SleepTo implements Operator
{
    /**
     * @var Context|null
     */
    protected $to;

    /**
     * @var string|null
     */
    protected $wakeId;

    /**
     * @var int
     */
    protected $gcTurn;

    /**
     * SleepTo constructor.
     * @param Context|null $to
     * @param null|string $wakeId
     * @param int $gcTurn
     */
    public function __construct(?Context $to, ?string $wakeId, int $gcTurn)
    {
        $this->to = $to;
        $this->wakeId = $wakeId;
        $this->gcTurn = $gcTurn;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();

        $challenger = null;
        // context 生成新的 Thread 是高优先级
        if (isset($this->to)) {
            $challenger = new IThread($this->to->toNewNode());

        // 尝试 wake 一个已经存在的 sleeping thread
        } elseif (isset($this->wakeId)) {
            $challenger = $process->popBlocking() ?? $process->popSleeping();

        }

        // 如果挑战者不存在, 则当前 sleep 不成立, 将走向 quit
        if (!isset($challenger)) {
            return new QuitSession();
        }

        // 用需要占据 current 的 thread 去 replace
        $popped = $process->replaceAliveThread($challenger);

        // 如果是要设置 gc, 丢进 gc 的周期
        if ($this->gcTurn > 0) {
            $process->addGcThread($popped, $this->gcTurn);

        // 加入到 sleeping 的队列里.
        } else {
            $process->addSleepingThread($popped, true);
        }

        // wake 新的 current
        return new WakeStage();
    }


}