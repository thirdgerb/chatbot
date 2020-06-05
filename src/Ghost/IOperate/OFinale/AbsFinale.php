<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OFinale;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Operate\Finale;
use Commune\Blueprint\Ghost\Runtime\Process;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsFinale extends AbsOperator implements Finale
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * AbsFinale constructor.
     * @param Dialog $dialog
     */
    public function __construct(Dialog $dialog)
    {
        parent::__construct($dialog);
        $this->process = $dialog->process;
        $this->cloner = $dialog->cloner;
    }

    public function getDialog(): Dialog
    {
        return $this->dialog;
    }


    protected function setProcess(Process $process) : void
    {
        $this->process = $process;
        $this->cloner->runtime->setCurrentProcess($process);
    }

    protected function runAwait(bool $silent = false ) : void
    {
        $process = $this->process;
        $waiter = $process->waiter;

        if (!isset($waiter) || $silent) {
            return;
        }

        // 如果是 waiter, 重新输出 question
        $question = $waiter->question;
        $input = $this->cloner->input;

        if (isset($question)) {
            $this->cloner->output($input->output($question));
        }

        // 尝试同步状态变更.
        $contextMsg = $this->cloner->runtime->toContextMsg();

        if (isset($contextMsg)) {
            $this->cloner->output($input->output($contextMsg));
        }
    }



}