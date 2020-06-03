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
use Commune\Ghost\Dialog\IReceive;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Protocals\HostMsg\ConvoMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OStart extends AbsOperator
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var IReceive
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
        $this->cloner = $cloner;
        $this->process = $cloner->runtime->getCurrentProcess();
        $this->start = $this->process->getAwait() ?? $this->process->getRoot();
        $dialog = new IReceive($this->cloner, $this->start, null);
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $process = $this->process;
        // 先 activate
        $process->activate($this->start);
        $input = $this->cloner->input;


        // 检查是否是异步 yielding 消息
        $operator = $this->checkAsyncInput($input)
            // 检查是否是强制同步状态的 contextMsg
            ?? $this->isContextMsgCall($input)
            // 检查是否有阻塞中的任务.
            ?? $this->checkBlocking()
            // 检查是否是 session 第一次输入, 是的话要初始化 session
            ?? $this->isSessionStart()
            // 如果不是影响对话状态的 convo msg, 则全部由 await ucl 来处理. 不走任何理解和路由.
            ?? $this->isNotConvoMsgCall($input)
            // 通过管道试图理解消息, 将理解结果保留在 comprehension 中.
            ?? $this->runComprehendPipes($input)
            // 问题匹配
            ?? $this->checkQuestion()
            // 检查是否命中了路由.
            ?? $this->checkAwaitRoutes()
            ?? $this->heed();

        return $operator;
    }

    protected function destroy(): void
    {
        $this->cloner = null;
        $this->process = null;
        $this->start = null;
        parent::destroy();
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
        return $this->dialog->redirectTo($context->getUcl());
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


    protected function isSessionStart() : ? Operator
    {
        $process = $this->process;

        // 没有 waiter, 说明是 session 初始化
        if ($process->isFresh()) {

            // 先 reactivate
            $reactivate = $this->dialog->reactivate();

            $creator = function(Dialog $dialog) {
                if (!$dialog->process->isFresh()) {
                    return new OStart($dialog->cloner);
                }
                return null;
            };

            // 然后重新启动.
            return new BridgeOperator($reactivate, $creator);
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

    protected function runComprehendPipes(InputMsg $input) : ? Operator
    {

        $awaitUcl = $this->start;
        $contextDef = $awaitUcl->findContextDef($this->cloner);

        $pipes = $contextDef->comprehendPipes($this->dialog);

        if (is_null($pipes)) {
            return $this->runGhostComprehendPipes($input);
        }

        $this->runComprehendPipeline($input, $pipes);
        return null;
    }

    protected function checkQuestion() : ? Operator
    {
        $question = $this->process->getAwaitQuestion();
        if (empty($question)) {
            return null;
        }

        $question->parse($this->cloner);

        return null;
    }

    protected function runGhostComprehendPipes(InputMsg $input) : ? Operator
    {
        $pipes = $this->cloner->ghost->getConfig()->comprehensionPipes;
        $this->runComprehendPipeline($input, $pipes);
        return null;
    }

    protected function runComprehendPipeline(InputMsg $input, array $pipes) : void
    {
        if (empty($pipes)) {
            return;
        }

        $pipeline = $this->cloner->buildPipeline(
            $pipes,
            ComprehendPipe::HANDLE,
            function(InputMsg $input) : InputMsg{
                return $input;
            });

        $input = $pipeline($input);
        $this->cloner->replaceInput($input);
    }


    /*--------- intent match ---------*/

    protected function checkAwaitRoutes() : ? Operator
    {
        $routes = $this->process->getAwaitRoutes();

        if (empty($routes)) {
            return null;
        }

        $matched = $this->matchAwaitRoutes(...$routes);

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

    protected function matchAwaitRoutes(Ucl ...$contexts) : ? Ucl
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
        $stageDef = $this->start->findStageDef($this->cloner);
        $next = $stageDef->onReceive($this->dialog);
        return $next;
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
    public function getName(): string
    {
        return static::class;
    }


    public function getDialog(): Dialog
    {
        return $this->dialog;
    }

    public function __invoke(): Operator
    {
        return $this;
    }


}