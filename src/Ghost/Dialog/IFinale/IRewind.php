<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IFinale;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Blueprint\Ghost\Dialog\Finale\Rewind;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRewind extends AbsDialogue implements Rewind
{
    const SELF_STATUS = self::FINALE;

    /**
     * @var bool
     */
    protected $silent;

    public function __construct(Cloner $cloner, Ucl $ucl, bool $silent = false)
    {
        $this->silent = $silent;
        parent::__construct($cloner, $ucl);
    }

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $this->ticked = true;
        return $this;
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $prev = $process->prev;
        $runtime = $this->cloner->runtime;
        if (isset($prev)) {
            $runtime->setCurrentProcess($process);
        }

        $process = $runtime->getCurrentProcess();
        $waiter = $process->waiter;
        if (!isset($waiter) || $this->silent) {
            return;
        }

        // 如果是 waiter, 重新输出 question
        $question = $waiter->question;
        $ghostInput = $this->cloner->ghostInput;
        if (isset($question)) {
            $this->cloner->output($ghostInput->output($question));
        }

        // 尝试同步状态变更.
        $contextMsg = $runtime->toContextMsg();
        if (isset($contextMsg)) {
            $this->cloner->output($ghostInput->output($contextMsg));
        }
    }


}