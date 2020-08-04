<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\ORedirect;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IResume\ICallback;
use Commune\Ghost\Dialog\IResume\IPreempt;
use Commune\Ghost\Dialog\IResume\IRestore;
use Commune\Ghost\Dialog\IResume\IWake;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Message\Host\SystemInt\DialogYieldInt;
use Commune\Ghost\Dialog\IActivate\IRedirect;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ORedirectTo extends AbsRedirect
{
    /**
     * @var Ucl
     */
    protected $target;

    /**
     * @var bool
     */
    protected $intentional;

    public function __construct(Dialog $dialog, Ucl $target, bool $noBlocking)
    {
        $this->target = $target;
        $this->intentional = $noBlocking;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $current = $this->dialog->ucl;

        $target = $this->target;
        if ($current->isSameContext($target)) {
            return $current->stageName === $target->stageName
                ? $this->dialog->reactivate()
                : $this->dialog->goStage($target->stageName);
        }

        // 如果不是故意触发的redirect, 则会尝试将当前会话保留到 blocking 栈.
        if (!$this->intentional) {
            $priority = $current
                ->findContextDef($this->dialog->cloner)
                ->getPriority();
            $targetPriority = $target
                ->findContextDef($this->dialog->cloner)
                ->getPriority();

            // priority > 0 的语境都不允许被抢占行为终结.
            // 将当前对话压入 blocking 栈.
            if ($priority > 0 || $priority >= $targetPriority) {
                $this->dialog->process->addBlocking($current, $priority);
            }
        }

        // 重定向到目标语境.
        $task = $this->dialog->process->getTask($target);
        $status = $task->getStatus();
        switch ($status) {
            case Context::CALLBACK:
                $resume = new ICallback($this->dialog, $target);
                return $this->resume($resume, $target);

            case Context::BLOCKING :
                $resume = new IPreempt($this->dialog, $target);
                return $this->resume($resume, $target);

            case Context::DEPENDING :
                $depending = $this->dialog
                    ->process
                    ->getDepended($target->getContextId());

                if (isset($depending)) {
                    return new ORedirectTo($this->dialog, $depending, true);
                }

                return $this->redirect($this->target, function(Ucl $target) {
                    return new IRedirect($this->dialog, $target);
                });

            case Context::SLEEPING :
                $resume = new IWake($this->dialog, $target);
                return $this->resume($resume, $target);

            case Context::YIELDING :

                $this->dialog
                    ->send()
                    ->message(DialogYieldInt::instance($target->encode()));

                return $this->dialog->rewind();

            case Context::DYING :
                $resume = new IRestore($this->dialog, $target);
                return $this->resume($resume, $target);

            case Context::AWAIT :
            case Context::CREATED :
            default :
                return $this->redirect($this->target, function(Ucl $target){
                    return new IRedirect($this->dialog, $target);
                });
        }
    }

}