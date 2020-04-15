<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Fallback;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Retrace\FulfillRetrace;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FulfillCurrent extends AbsOperator
{
    /**
     * @var int
     */
    protected $gcTurn;

    /**
     * FulfillCurrent constructor.
     * @param int $gcTurn
     */
    public function __construct(int $gcTurn = 0)
    {
        $this->gcTurn = $gcTurn;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getProcess();
        $thread = $process->aliveThread();
        $currentNode = $thread->currentNode();

        $currentContext = $currentNode->findContext($conversation);

        // 回退成功, 说明要执行上级节点
        $pop = $thread->popNode();
        if (isset($pop)) {
            return new FulfillRetrace($thread, $currentContext);
        }

        // 否则, 检查 block
        return new FallbackToBlock($process, $this->gcTurn);
    }


}