<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\StartProcess;
use Commune\Blueprint\Ghost\Pipe\ComprehendPipe;
use Commune\Blueprint\Ghost\Runtime\RoutesMap;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate\IStaging;
use Commune\Ghost\Dialog\Traits\TIntentMatcher;
use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Protocals\Host\ConvoMsg;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\RetainInput;
use Commune\Protocals\Intercom\YieldInput;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStartProcess extends AbsDialogue implements StartProcess
{
    use TIntentMatcher;

    public function __construct(Cloner $cloner)
    {
        $this->process = $cloner->runtime->getCurrentProcess();
        $ucl = $this->process->awaiting ?? $this->process->root;
        parent::__construct($cloner, Ucl::decodeUcl($ucl));
    }


    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function selfActivate(): void
    {
        return;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $map = $process->buildRoutes();
        $input = $this->cloner->ghostInput;

        // 检查是否是异步 yielding 消息
        return $this->checkYield($input)
            // 检查是否是异步 retain 消息
            ?? $this->checkRetain($input)
            // 检查是否有阻塞中的任务.
            ?? $this->checkBlocking()
            // 检查是否是 session 第一次输入, 是的话要初始化 session
            ?? $this->shouldStartSession()
            // 检查是否是强制同步状态的 contextMsg
            ?? $this->isContextMsgCall($input)
            // 如果不是影响对话状态的 convo msg, 则全部由 await ucl 来处理. 不走任何理解和路由.
            ?? $this->isNotConvoMsgCall($input)
            // 通过管道试图理解消息, 将理解结果保留在 comprehension 中.
            ?? $this->runComprehendPipes()
            // 检查是否命中了 watching 路由.
            ?? $this->checkWatching($map)
            // 检查是否命中了 stage 路由
            ?? $this->checkStageRoutes($map)
            // 检查是否命中了 context 路由.
            ?? $this->checkContextRoutes($map)
            // 啥都没有的时候, 让 await ucl 来处理.
            ?? $this->heedByAwaitingUcl();
    }

    /*--------- start session ---------*/

    protected function shouldStartSession()
    {
        $process = $this->getProcess();
        $awaitUclStr = $process->awaiting;

        // 没有 await, 说明是 session 初始化
        if (empty($awaitUclStr)) {
            return DialogHelper::newDialog(
                $this,
                $process->decodeUcl($process->root),
                Dialog\Activate\StartSession::class
            );
        }

        return null;
    }

    /*--------- special handler ---------*/

    protected function isContextMsgCall(GhostInput $input) : ? Dialog
    {
        $message = $input->getMessage();

        // 同步终态的 Context
        if (!$message instanceof ContextMsg) {
            return null;
        }

        $context = $message->toContext($this->cloner);
        return $this->then()->home($context->getUcl());
    }

    protected function isNotConvoMsgCall(GhostInput $input) : ? Dialog
    {
        $message = $input->getMessage();
        if ($message->isProtocal(ConvoMsg::class)) {
            return null;
        }
        // 直接让 waiter 处理, 不走任何路由和理解.
        return $this->heedByAwaitingUcl();
    }


    /*--------- await ---------*/

    protected function heedByAwaitingUcl() : Dialog
    {
        $process = $this->getProcess();
        $awaitUclStr = $process->awaiting;
        $ucl = $process->decodeUcl($awaitUclStr);

        return DialogHelper::newDialog(
            $this,
            $ucl,
            Dialog\Retain\Heed::class
        );
    }

    /*--------- context intercept ---------*/

    protected function checkContextRoutes(RoutesMap $map) : ? Dialog
    {
        $contextRoutes = $map->contextRoutes;
        if (empty($contextRoutes)) {
            return null;
        }

        $target = null;
        foreach ($contextRoutes as $contextNameOrUcl) {

            if (StringUtils::isWildCardPattern($contextNameOrUcl)) {
                $matched = $this->wildCardIntentNameMatch($contextNameOrUcl);
                $matchedContextName = $this->checkMatchedStageNameExists($this->ucl, $matched, true);

                if (isset($matchedContextName)) {
                    $target = Ucl::create($this->cloner, $matchedContextName, null);
                    break;
                }

            } else {
                $ucl = Ucl::create($this->cloner, $contextNameOrUcl, null);

                if ($this->exactStageIntentMatch($ucl)) {
                    $target = $ucl;
                    break;
                }
            }
        }

        if (isset($target)) {
            return DialogHelper::newDialog(
                $this,
                $target,
                Dialog\Activate\Intend::class
            );
        }
        return null;
    }

    /*--------- staging intercept ---------*/

    protected function checkStageRoutes(RoutesMap $map) : ? Dialog
    {
        $stageRoutes = $map->stageRoutes;
        if (empty($stageRoutes)) {
            return null;
        }

        $process = $this->getProcess();
        $awaitUcl = $process->decodeUcl($process->awaiting);

        // 设置重定向的目标.
        $staging = $this->stageRoutesMatch($awaitUcl, $stageRoutes);
        if (isset($staging)) {
            // 触发 staging 事件. 重定向到另一个 stage.
            return new IStaging($this->cloner, $staging);
        }

        return null;
    }

    protected function checkWatching(RoutesMap $map) : ? Dialog
    {
        $watching = $map->watching;
        if (empty($watching)) {
            return null;
        }

        $process = $this->getProcess();
        foreach ($watching as $watchingUclStr) {
            $watchingUcl = $process->decodeUcl($watchingUclStr);

            if ($this->exactStageIntentMatch($watchingUcl)) {

                // 触发 watch 事件.
                return DialogHelper::newDialog(
                    $this,
                    $watchingUcl,
                    Dialog\Retain\Watch::class
                );
            }
        }

        return null;
    }


    /*--------- comprehend pipeline ---------*/

    protected function runComprehendPipes() : ? Dialog
    {
        $process = $this->getProcess();
        $awaitUclStr = $process->awaiting;

        if (empty($awaitUclStr)) {
            return $this->runGhostComprehendPipes();
        }

        $awaitUcl = $process->decodeUcl($awaitUclStr);
        $stageDef = $awaitUcl->findStageDef($this->cloner);

        $pipes = $stageDef->comprehendPipes($this->cloner);
        if (is_null($pipes)) {
            return $this->runGhostComprehendPipes();
        }


        $this->runComprehendPipeline($pipes);
        return null;
    }

    protected function runGhostComprehendPipes() : ? Dialog
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

        $pipeline = $this->cloner->buildPipeline($pipes, ComprehendPipe::HANDLE, function($cloner){
            return $cloner;
        });
        $this->cloner = $pipeline($this->cloner);
    }

    protected function checkBlocking() : ? Dialog
    {
        $process = $this->getProcess();
        $blockingUclStr = $process->popBlocking();
        if (empty($blockingUclStr)) {
            return null;
        }
        $blockingUcl = $process->decodeUcl($blockingUclStr);
        $context = $this->getContext($blockingUcl);
        return $this->challengeCurrent($context);
    }

    /*--------- async inputs ---------*/

    protected function checkRetain(GhostInput $input) : ? Dialog
    {
        if (!$input instanceof RetainInput) {
            return null;
        }

        $retainContext = $input->toContext($this->cloner);
        return $this->challengeCurrent($retainContext);
    }


    protected function checkYield(GhostInput $input) : ? Dialog
    {
        if (!$input instanceof YieldInput) {
            return null;
        }

        $yieldingContext = $input->toContext($this->cloner);
        return $this->challengeCurrent($yieldingContext);
    }

    /*--------- challenger ---------*/

    protected function challengeCurrent(Context $challenger) : ? Dialog
    {
        $challengerUcl = $challenger->getUcl();

        $process = $this->getProcess();
        $awaitUclStr = $process->awaiting;

        // 当前等待的 Context 为空, 则直接占领.
        if (empty($awaitUclStr)) {
            return DialogHelper::newDialog(
                $this,
                $challengerUcl,
                Dialog\Retain\Preempt::class
            );
        }

        $awaitUcl = $process->decodeUcl($awaitUclStr);
        $awaitContext = $this->getContext($awaitUcl);

        $challengerPriority = $challenger->getPriority();
        $awaitPriority = $awaitContext->getPriority();
        // 可以占据成功.
        if ($awaitPriority < $challengerPriority) {
            return DialogHelper::newDialog(
                $this,
                $challengerUcl,
                Dialog\Retain\Preempt::class
            );
        }

        // 否则将 yielding 对象加入 blocking 栈. 并继续.
        $process->addBlocking($challengerUcl, $challengerPriority);
        return null;

    }

}