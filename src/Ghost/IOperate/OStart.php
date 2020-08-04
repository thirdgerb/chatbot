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
use Commune\Ghost\IOperate\OExiting\OCancel;
use Commune\Ghost\IOperate\OExiting\OFulfill;
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
     * @var Process
     */
    protected $process;

    /**
     * @var Ucl
     */
    protected $start;

    /**
     * @var IReceive
     */
    protected $dialog;

    /**
     * OStart constructor.
     * @param Cloner $cloner
     */
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

        // 链式的启动流程.
        // 以下流程其实可以拆分成若干个独立的 Operator.
        // 但为什么没有这么做呢?
        // 主要原因是, 作者希望最核心的控制流程能够一目了然
        // 其次是减少 Trace 的轨迹长度, 将后续分支少的环节合并到相同的 operator.


        // 检查是否是强制同步状态的 contextMsg
        $operator = $this->isContextMsgCall($input);
            // ?? $this->checkBlocking()
        // 检查是否是 session 第一次输入, 是的话要初始化 session
        $operator = $operator ?? $this->isSessionStart();
        // 如果不是影响对话状态的 convo msg,
        // 则全部由 await ucl 来处理. 不走任何理解和路由.
        $operator = $operator ?? $this->isNotConvoMsgCall($input);
        // 以下是语义相关的环节.
        // 通过管道试图理解消息, 将理解结果保留在 comprehension 中.
        $operator = $operator ?? $this->runComprehendPipes($input);
        // 问题匹配
        $operator = $operator ?? $this->checkQuestion();
        // 检查是否命中了路由.
        $operator = $operator  ?? $this->checkAwaitRoutes();
        // 进行 listen 的逻辑.
        $operator = $operator ?? $this->heed();

        return $operator;
    }

    /*------ pipeline ------*/

    /**
     * 如果输入消息是一个 ContextMsg 对象.
     * @param InputMsg $input
     * @return Operator|null
     */
    protected function isContextMsgCall(InputMsg $input) : ? Operator
    {
        $message = $input->getMessage();

        // 同步终态的 Context
        if (!$message instanceof ContextMsg) {
            return null;
        }

        $target = $message->toContext($this->cloner);
        $targetUcl = $target->getUcl();

        $mode = $message->getMode();
        $current = $this->dialog->ucl;
        $currentDef = $current->findContextDef($this->cloner);

        switch($mode) {

            // 发起该 context 的cancel 流程.
            case ContextMsg::MODE_CANCEL:
                $this->process->addBlocking(
                    $current,
                    $currentDef->getPriority()
                );
                $dialog = new IReceive(
                    $this->cloner,
                    $targetUcl,
                    $this->dialog
                );
                return new OCancel($dialog);

            // 发起该 context 的fulfill 流程.
            case ContextMsg::MODE_FULFILL:
                $this->process->addBlocking(
                    $current,
                    $currentDef->getPriority()
                );
                $dialog = new IReceive(
                    $this->cloner,
                    $targetUcl,
                    $this->dialog
                );
                return new OFulfill($dialog, 0, []);

            case ContextMsg::MODE_BLOCKING:
                return $this->challengeCurrent($targetUcl);

            // 强行替换掉当前对话状态, 并且丢弃当前对话状态.
            // 仅仅在于客户端主导时才有这种做法.
            case ContextMsg::MODE_REDIRECT:
            default :
                return $this->dialog->redirectTo($targetUcl, false);

        }
    }
//
//    /**
//     * 新消息来检查, 是否有新的任务优先级高于上一轮对话的任务.
//     * @return Operator|null
//     */
//    protected function checkBlocking() : ? Operator
//    {
//        // 必须是无副作用的.
//        $blocking = $this->process->firstBlocking();
//        if (empty($blocking)) {
//            return null;
//        }
//
//        return $this->challengeCurrent($blocking);
//    }

    /**
     * 检查会话是不是新启动.
     * 如果是新启动的话, 则触发一次 activate 来产生欢迎语.
     * 然后把当前输入当成一个回复来响应.
     *
     * 这样某些场景直接可以用 bot 能接受的命令来启动 bot.
     * 不过更安全的做法是, 连接 bot 时先发一条 EventMsg (client.connect)
     *
     * @return Operator|null
     */
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

    /**
     * 系统的输入消息默认是 ConvoMsg
     * 也就是可以在 client 和 host 之间传播和渲染的消息 (conversation message)
     *
     * 除了 ConvoMsg 之外, 还有三种是带有明确语义的:
     *
     * - IntentMsg: 直接是意图.
     * - DirectiveMsg: 指令.
     * - ApiMsg: 调用 api. 理论上不会传到这里来.
     *
     * @param InputMsg $input
     * @return Operator|null
     */
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
//
//        $awaitUcl = $this->start;
//        $contextDef = $awaitUcl->findContextDef($this->cloner);
//
//        $pipes = $contextDef->comprehendPipes($this->dialog);
//
//        if (is_null($pipes)) {
//            return $this->runGhostComprehendPipes($input);
//        }
//
//        $this->runComprehendPipeline($input, $pipes);
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
        return null;
//        $pipes = $this->cloner->ghost->getConfig()->comprehensionPipes;
//        $this->runComprehendPipeline($input, $pipes);
//        return null;
    }

    // todo 也许不用管道来做.
    protected function runComprehendPipeline(InputMsg $input, array $pipes) : void
    {
        if (empty($pipes)) {
            return;
        }

        $pipeline = $this->cloner->buildPipeline(
            $pipes,
            ComprehendPipe::HANDLE,
            function(Cloner $cloner) : Cloner{
                return $cloner;
            });

        $pipeline($this->cloner);
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
        if ($awaitPriority <= $challengerPriority) {
            $this->process->addBlocking($this->start, $awaitPriority);
            return $this->dialog->redirectTo($challenger);

        // 无法抢占成功
        } else {
            $this->process->addBlocking($challenger, $challengerPriority);
            return $this->dialog->rewind(true);
        }
    }

    /*------ operator ------*/
    public function getName(): string
    {
        return static::class;
    }

    public function __invoke(): Operator
    {
        return $this;
    }


}