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
use Commune\Message\Host\SystemInt\DialogForbidInt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsRedirect extends AbsOperator
{

    protected function redirect(Ucl $target, callable $creator, array $insertPath = []) : Operator
    {
        $cloner = $this->dialog->cloner;

        // 权限校验.
        $auth = $target
            ->findContextDef($cloner)
            ->getStrategy($this->dialog)
            ->auth;


        if (!empty($auth)) {
            $await = $this->dialog->process->getAwait();
            foreach ($auth as $ability) {
                if (!$cloner->auth->allow($ability)) {
                    $dialog = $this
                        ->dialog
                        ->send()
                        ->message(DialogForbidInt::instance(
                            $target->contextName,
                            $ability
                        ))
                        ->over();
                    if ($await->isSameContext($target)) {
                        return $dialog->close();
                    } else {
                        return $dialog->rewind();
                    }
                }
            }
        }

        // 检查重定向是否被拦截.
        // 自身就不需要调用 redirect 方法.
        if (!$target->equals($this->dialog->ucl)) {
            $intercepted = $target
                ->findStageDef($this->dialog->cloner)
                ->onRedirect($this->dialog, $target);

            if (isset($intercepted )) {
                return $intercepted;
            }
        }


        $task = $this->dialog->process->getTask($target);
        // 如果目标 Context 是新建, 则需要从起点开始走. 否则不需要.
        if (
            $task->isStatus(Context::CREATED)
            && $task->getUcl()->stageName !== $target->stageName
        ) {
            array_unshift($insertPath, $target->stageName);
            $target = $target->goStage();
        }

        $redirect = $creator($target);
        return $this->activate($redirect, $insertPath);
    }

    protected function activate(Activate $iActivate, array $insertPath) : Operator
    {
        $cloner = $iActivate->cloner;
        $target = $iActivate->ucl;
        $def = $target->findStageDef($cloner);

        if (!empty($insertPath)) {
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
        if ($task->getUcl()->stageName !== $target->stageName) {
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