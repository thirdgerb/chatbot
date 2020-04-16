<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\End;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Message\Blueprint\QuestionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Await implements Operator
{
    /**
     * @var QuestionMsg|null
     */
    protected $question;

    /**
     * Await constructor.
     * @param QuestionMsg|null $question
     */
    public function __construct(QuestionMsg $question = null)
    {
        $this->question = $question;
    }

    public function invoke(Conversation $conversation): ? Operator
    {
        $runtime = $conversation->runtime;
        // 获取线程
        $thread = $runtime->getCurrentProcess()->aliveThread();

        // 准备好 question
        if (isset($this->question)) {
            $thread->setQuestion($this->question);
        }

        // 准备好 ContextMsg, 用于同步状态.
        $contextMsg = $runtime->toContextMsg();
        if (isset($contextMsg)) {
            $conversation->output($contextMsg);
        }

        return null;
    }


}