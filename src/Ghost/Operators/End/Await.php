<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\End;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Message\Blueprint\QuestionMsg;
use Commune\Message\Predefined\IContextMsg;


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

    public function invoke(Cloner $cloner): ? Operator
    {
        $runtime = $cloner->runtime;
        $process = $runtime->getCurrentProcess();
        // 获取线程
        $thread = $process->aliveThread();

        // 准备好 question
        if (isset($this->question)) {
            $thread->setQuestion($this->question);
        }

        // 准备好 ContextMsg, 用于同步状态.
        $prev = $process->prev();
        if (isset($prev)) {
            // 如果状态有变更的话.
            $node = $process->compareContext($prev);
            if (isset($node)) {
                $context = $node->findContext($cloner);
                $contextMsg = new IContextMsg(
                    $context->getId(),
                    $context->getName(),
                    $context->toEntities()
                );
                $cloner->output($contextMsg);
            }
        }

        return null;
    }


}