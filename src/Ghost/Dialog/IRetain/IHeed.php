<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IRetain;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Retain\Heed;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Blueprint\Ghost\Pipe\ComprehendPipe;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Ghost\Runtime\Operators\BridgeOperator;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Protocals\HostMsg\ConvoMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHeed extends AbsDialog implements Heed
{
    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $input = $this->cloner->input;

        // 检查是否是异步 yielding 消息
        return $this->checkAsyncInput($input)
            // 检查是否是强制同步状态的 contextMsg
            ?? $this->isContextMsgCall($input)
            // 检查是否有阻塞中的任务.
            ?? $this->checkBlocking()
            // 检查是否是 session 第一次输入, 是的话要初始化 session
            ?? $this->shouldStartSession()
            // 如果不是影响对话状态的 convo msg, 则全部由 await ucl 来处理. 不走任何理解和路由.
            ?? $this->isNotConvoMsgCall($input, $process)
            // 通过管道试图理解消息, 将理解结果保留在 comprehension 中.
            ?? $this->runComprehendPipes()
            // 检查是否命中了 watching 路由.
            ?? $this->checkWatching($process)
            // 检查是否命中了 stage 路由
            ?? $this->checkStageRoutes($process)
            // 检查是否命中了 context 路由.
            ?? $this->checkContextRoutes($process)
            // 啥都没有的时候, 让 await ucl 来处理.
            ?? $this->selfHeed($process);
    }


    /*--------- flow ---------*/

    protected function checkAsyncInput(InputMsg $input) : ? Operator
    {
        // todo 带了场景再实现
        return null;
    }

    protected function isContextMsgCall(InputMsg $input) : ? Operator
    {
        $message = $input->getMessage();

        // 同步终态的 Context
        if (!$message instanceof ContextMsg) {
            return null;
        }

        $context = $message->toContext($this->cloner);
        // 直接重定向到目标位置.
        return $this->nav()->redirectTo($context->toUcl());
    }

    protected function checkBlocking() : ? Operator
    {
        $process = $this->getProcess();
        // 必须是无副作用的.
        $blocking = $process->firstBlocking();
        if (empty($blocking)) {
            return null;
        }

        return $this->challengeCurrent($blocking);
    }


    protected function shouldStartSession()
    {
        $process = $this->getProcess();

        // 没有 waiter, 说明是 session 初始化
        if ($process->isFresh()) {

            $reactivate = $this->nav()->reactivate();

            $heed = function(Dialog $dialog) {
                $process = $dialog
                    ->cloner
                    ->runtime
                    ->getCurrentProcess();

                $ucl = $process->getAwaiting();
                return new IHeed($dialog->cloner, $ucl, $dialog);
            };

            return new BridgeOperator($reactivate, $heed);
        }

        return null;
    }

    protected function isNotConvoMsgCall(InputMsg $input, Process $process) : ? Operator
    {
        $message = $input->getMessage();
        if ($message instanceof ConvoMsg) {
            return null;
        }

        // 直接让 waiter 处理, 不走任何路由和理解.
        return $this->selfHeed($process);
    }

    /*--------- comprehendPipes ---------*/

    protected function runComprehendPipes() : ? Operator
    {

        $awaitUcl = $this->_ucl;
        $contextDef = $awaitUcl->findContextDef($this->cloner);

        $pipes = $contextDef->comprehendPipes($this);

        if (is_null($pipes)) {
            return $this->runGhostComprehendPipes();
        }

        $this->runComprehendPipeline($pipes);
        return null;
    }


    protected function runGhostComprehendPipes() : ? Operator
    {
        $pipes = $this->cloner->ghost->getConfig()->comprehensionPipes;
        $this->runComprehendPipeline($pipes);
        return null;
    }

    protected function runComprehendPipeline(array $pipes) : void
    {
        if (empty($pipes)) {
            return;
        }

        $pipeline = $this->cloner->buildPipeline(
            $pipes,
            ComprehendPipe::HANDLE,
            function($cloner){
                return $cloner;
            });

        $this->cloner = $pipeline($this->cloner);
    }

    /*--------- watching ---------*/

    protected function checkWatching(Process $process) : ? Operator
    {
        foreach ($process->eachWatchers() as $ucl) {
            $intentDef = $ucl->findIntentDef($this->_cloner);
            if ($intentDef->match($this->_cloner)) {
                return new IActivate\IWatch($this->_cloner, $ucl, $this);
            }
        }

        return null;
    }

    /*--------- intent match ---------*/

    protected function checkStageRoutes(Process $process) : ? Operator
    {
        $stages = $process->getAwaitStageNames();
        if (empty($stages)) {
            return null;
        }

        $matcher = $this->cloner->matcher;
        foreach ($stages as $stage) {
            $intentName = $this->_ucl->toStageIntentName($stage);
            if ($matcher->matchStageOfIntent($intentName)) {
                $ucl = $this->_ucl->goStage($stage);
                return new IActivate\IStaging(
                    $this->_cloner,
                    $ucl,
                    $this
                );
            }
        }

        return null;
    }

    protected function checkContextRoutes(Process $process) : ? Operator
    {
        $contexts = $process->getAwaitContexts();

        if (empty($contexts)) {
            return null;
        }

        $matcher = $this->cloner->matcher->refresh();

        foreach ($contexts as $ucl) {

            $intentName = $ucl->toStageIntentName();
            if ($matcher->matchStageOfIntent($intentName)->truly()) {
                $target = $ucl->goStageByIntentName($intentName);
                return $this->nav()->redirectTo($target);
            }
        }

        return null;
    }

    /*--------- heed ---------*/

    protected function selfHeed(Process $process) : Operator
    {
        $process->unsetWaiting($this->_ucl);
        $stageDef = $this->_ucl->findStageDef($this->_cloner);
        return $stageDef->onRetain($this);
    }



    /*--------- methods ---------*/

    protected function challengeCurrent(Ucl $challenger) : ? Operator
    {
        $challengerPriority = $challenger
            ->findContextDef($this->_cloner)
            ->getPriority();

        $awaitPriority = $this->_ucl
            ->findContextDef($this->_cloner)
            ->getPriority();

        // 可以占据成功.
        if ($awaitPriority < $challengerPriority) {

            return new IActivate\IPreempt(
                $this->_cloner,
                $challenger,
                $this
            );
        }

        return null;

    }

}