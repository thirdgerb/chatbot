<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Pipe\ComprehendPipe;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IRetain\IReceive;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Protocals\HostMsg\ConvoMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OStart implements Operator
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var Ucl
     */
    protected $start;

    public function __construct(Cloner $cloner)
    {
        $this->process = $cloner->runtime->getCurrentProcess();
        $this->start = $this->process->getAwait() ?? $this->process->getRoot();
        $this->dialog = new IReceive($this->cloner, $this->start, null);
    }

    public function tick(): Operator
    {
        $process = $this->process;
        // 先 activate
        $process->activate($this->start);
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
            ?? $this->isNotConvoMsgCall($input)
            // 通过管道试图理解消息, 将理解结果保留在 comprehension 中.
            ?? $this->runComprehendPipes()
            // 检查是否命中了 stage 路由
            ?? $this->checkStageRoutes()
            // 检查是否命中了 context 路由.
            ?? $this->checkContextRoutes()
            // 啥都没有的时候, 让 await ucl 来处理.
            ?? $this->heed();
    }

    /*------ pipeline ------*/

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
        return $this->dialog->redirectTo($context->toUcl());
    }

    protected function checkBlocking() : ? Operator
    {
        // 必须是无副作用的.
        $blocking = $this->process->firstBlocking();
        if (empty($blocking)) {
            return null;
        }

        return $this->challengeCurrent($blocking);
    }


    protected function shouldStartSession()
    {
        $process = $this->process;

        // 没有 waiter, 说明是 session 初始化
        if ($process->isFresh()) {

            // 先 reactivate
            $reactivate = $this->dialog->reactivate();

            // 然后重新启动.
            return new BridgeOperator($reactivate, function(Dialog $dialog) {
                $await = $dialog->process->getAwait();
                if (isset($await)) {
                    return new OStart($dialog->cloner);
                }

                return null;
            });
        }

        return null;
    }

    protected function isNotConvoMsgCall(InputMsg $input) : ? Operator
    {
        $message = $input->getMessage();
        if ($message instanceof ConvoMsg) {
            return null;
        }

        // 直接让 waiter 处理, 不走任何路由和理解.
        return $this->heed();
    }

    /*------ comprehend pipes ------*/

    protected function runComprehendPipes() : ? Operator
    {

        $awaitUcl = $this->start;
        $contextDef = $awaitUcl->findContextDef($this->cloner);

        $pipes = $contextDef->comprehendPipes($this->dialog);

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


    /*--------- intent match ---------*/

    protected function checkStageRoutes() : ? Operator
    {
        $stages = $this->process->getAwaitStageNames();

        if (empty($stages)) {
            return null;
        }

        $matched = $this->matchStageRoutes($this->start, $stages);

        return isset($matched)
            ? $this->dialog->next($matched->stageName)
            : null;
    }

    protected function checkContextRoutes() : ? Operator
    {
        $contexts = $this->process->getAwaitContexts();

        if (empty($contexts)) {
            return null;
        }

        $matched = $this->matchContextRoutes(...$contexts);

        return isset($matched)
            ? $this->dialog->redirectTo($matched)
            : null;
    }

    protected function matchStageRoutes(Ucl $current, array $stages = []) : ? Ucl
    {
        $matcher = $this->cloner->matcher->refresh();
        foreach ($stages as $stage) {
            $stageName = $current->getStageFullname($stage);
            if ($matcher->matchStage($stageName)->truly()) {
                return $current->goStage($stage);
            }
        }

        return null;
    }

    protected function matchContextRoutes(Ucl ...$contexts) : ? Ucl
    {
        $matcher = $this->cloner->matcher->refresh();

        foreach ($contexts as $ucl) {
            // 这个 ucl 可能是假的, 用了通配符
            $fullname = $ucl->getStageFullname();
            if ($matcher->matchStage($fullname)->truly()) {
                // 这个 ucl 就是真的了.
                return $ucl->goStageByFullname($fullname);
            }
        }

        return null;
    }

    /*------ heed ------*/

    protected function heed() : Operator
    {
        return $this->start
            ->findStageDef($this->cloner)
            ->onReceive($this->dialog);

    }

    /*------ methods ------*/

    protected function challengeCurrent(Ucl $challenger) : ? Operator
    {
        $challengerPriority = $challenger
            ->findContextDef($this->cloner)
            ->getPriority();

        $awaitPriority = $this->start
            ->findContextDef($this->cloner)
            ->getPriority();

        // 可以占据成功.
        if ($awaitPriority < $challengerPriority) {
            $this->process->addBlocking($this->start, $awaitPriority);
            return $this->dialog->redirectTo($challenger);
        }

        return null;
    }

    /*------ operator ------*/

    public function getDialog(): Dialog
    {
        return $this->dialog;
    }

    public function __invoke(): Operator
    {
        return $this;
    }


}