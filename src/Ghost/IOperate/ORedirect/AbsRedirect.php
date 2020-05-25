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
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsRedirect extends AbsOperator
{

    protected function redirect(Ucl $target, callable $creator) : Operator
    {
        $task = $this->dialog->process->getTask($target);
        $insertPath = [];
        // 如果目标 Context 是新建, 则需要从起点开始走.
        if (
            $task->isStatus(Context::CREATED)
            && $task->stage !== $target->stageName
        ) {
            $insertPath = [$target->stageName];
            $target = $target->goStage();
        }

        $redirect = $creator($target);
        unset($creator);

        return $this->activate($redirect, $insertPath);
    }

    protected function activate(Activate $iActivate, array $insertPath) : Operator
    {
        $target = $iActivate->ucl;
        $def = $target->findStageDef($this->dialog->cloner);

        // 检查拦截
        $next =  $def->onRedirect($iActivate->prev, $iActivate);

        if (isset($next)) {
            return $next;
        }

        if (empty($insertPath)) {
            $iActivate->task->insertPaths($insertPath);
        }

        // 正式运行.
        $iActivate->process->activate($target);
        return $def->onActivate($iActivate);
    }

    protected function resume(Resume $resume, Ucl $target) : Operator
    {
        $task = $resume->task;

        $insertPaths = [];
        if ($task->stage !== $target->stageName) {
            $insertPaths = [$target->stageName];
        }


        $def = $target->findStageDef($resume->cloner);

        $next = $def->onResume($resume);
        if (isset($next)) {
            return $next;
        }

        if (!empty($insertPaths)) {
            $task->insertPaths($insertPaths);
        }

        $resume->process->activate($target);
        return $resume instanceof Resume\Preempt
            ? $resume->reactivate()
            : $resume->next();
    }


}