<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Exceptions\DataNotFoundException;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Psr\Log\LoggerInterface;

/**
 * @property-read array $breakpointArr
 * @property-read Tracker $tracker
 */
class History implements RunningSpy
{
    use RunningSpyTrait;

    /**
     * @var Breakpoint
     */
    protected $breakpoint;

    /**
     * @var Breakpoint
     */
    protected $prevBreakpoint;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * History constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->sessionId = $session->sessionId;
        $this->logger = $session->logger;

        $snapshot = $this->session->repo->snapshot;
        $this->prevBreakpoint = $snapshot->breakpoint;

        //重新赋值
        $this->setBreakpoint(new Breakpoint($session, $this->prevBreakpoint));
        $this->tracker = new Tracker($session->sessionId);

        static::addRunningTrace($this->sessionId, $this->sessionId);
    }

    /**
     * @return Breakpoint|null
     */
    public function prev()
    {
        if (isset($this->prevBreakpoint)) {
            return $this->prevBreakpoint;
        }

        $prevId = $this->breakpoint->prevId;
        if (!isset($prevId)) {
            return null;
        }
        $breakpoint = $this->session->repo->fetchSessionData(
            $identity = new SessionDataIdentity(
                $this->breakpoint->prevId,
                SessionData::BREAK_POINT
            )
        );
        if (!$breakpoint instanceof Breakpoint) {
            throw new DataNotFoundException($identity);
        }

        return $this->prevBreakpoint = $breakpoint;
    }


    public function currentTask() : Node
    {
        return $this->breakpoint->process()->currentTask();
    }

    public function currentQuestion() : ? Question
    {
        return $this->breakpoint->process()->currentQuestion();
    }

    public function setQuestion(Question $question = null) : void
    {
        $this->breakpoint->process()->setQuestion($question);
    }

    /**
     * @return Context
     */
    public function getCurrentContext() : Context
    {
        $task = $this->currentTask();
        $id = $task->getContextId();

        $context = $this->session
            ->repo
            ->fetchSessionData(
                $identity = new SessionDataIdentity(
                    $id,
                    SessionData::CONTEXT_TYPE
                )
            );


        if (!isset($context) || !$context instanceof Context) {
            throw new DataNotFoundException($identity);
        }

        return $context;
    }

    public function getBreakPoint() : Breakpoint
    {
        return $this->breakpoint;
    }

    /*------ stage ------*/

    public function goStage(string $stageName, bool $reset) : History
    {
        $this->breakpoint->process()->goStage($stageName, $reset);
        return $this;
    }

    public function addStage(string $stage)  : History
    {
        $this->breakpoint->process()->addStage($stage);
        return $this;
    }


    /*------ 后退 ------*/

    /**
     * 返回到用户所见上一个问题的状态.
     * @return History|null
     * @throws DataNotFoundException
     */
    public function backward() : ? History
    {
        $lastId = $this->breakpoint->backward();
        if (!isset($lastId)) {
            return null;
        }

        $breakpoint = $this->session->repo->fetchSessionData(
            $identity = new SessionDataIdentity(
                $lastId,
                SessionData::BREAK_POINT
            )
        );

        if (!$breakpoint instanceof Breakpoint) {
            throw new DataNotFoundException($identity);
        }

        $this->prevBreakpoint = null;
        $this->setBreakpoint($breakpoint);
        return $this;
    }

    public function setBreakpoint(Breakpoint $breakpoint) : void
    {
        $this->breakpoint = $breakpoint;
        $this->session->repo->snapshot->breakpoint = $breakpoint;
    }

    /*------ 前进 ------*/

    /**
     * 遗忘掉当前thread, 进入一个新的thread
     *
     * @param Context $context
     * @return History
     */
    public function replaceThreadTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $newThread = new Thread(new Node($context));
        $this->breakpoint->process()->replaceThread($newThread);
        return $this;
    }

    /**
     * 将当前node 替换成新的context
     * 这要求新的context 作为回调, 继承旧的context class
     * @param Context $context
     * @return History
     */
    public function replaceNodeTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $this->breakpoint->process()->replaceNode(new Node($context));
        return $this;
    }

    public function replaceProcessTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $process = $this->homeProcess();
        $process->sleepTo(new Thread(new Node($context)));
        $this->breakpoint->replaceProcess($process);
        return $this;
    }

    protected function homeProcess() : Process
    {
        return new Process(
            $this->session->sessionId,
            new Thread(new Node($this->session->makeRootContext()))
        );
    }

    /**
     * 当前的context依赖新的context
     * 新的context完成后会回调当前context, 并按需赋值.
     *
     * @param Context $context
     * @return History
     */
    public function dependOn(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $task = new Node($context);
        $this->breakpoint->process()->dependOn($task);
        return $this;
    }

    /**
     * 暂停当前thread. 新thread fulfill 的时候, 可能被重新唤醒.
     * 优先级高于 sleep, 并且先入先出
     *
     * @param Context $context
     * @return History|null 不能sleep 的情况. 则直接运行.
     */
    public function sleepTo(Context $context = null) : ? History
    {
        $newThread = null;
        // 有目的地的时候.
        if (isset($context)) {
            $context = $this->wrapContext($context);
            $newThread = new Thread(new Node($context));
            $this->breakpoint->process()->sleepTo($newThread);
            return $this;
        }

        $target = $this->breakpoint->process()->wake();
        if (!empty($target)) {
            $this->breakpoint->process()->sleepTo($target);
            return $this;
        }

        return null;
    }

    protected function wrapContext(Context $context) : Context
    {
        return $context->toInstance($this->session);
    }

    /**
     * 暂停当前的thread. 等待服务的回调.
     * 只有接受到回调的时候, 才可能唤醒.
     *
     * yield 和 replace 的区别: yield 可以重新被唤醒. replace 就彻底遗忘了.
     *
     * @param Context|null $context
     * @return History
     */
    public function yieldTo(Context $context = null) : History
    {
        if (isset($context)) {
            $context = $this->wrapContext($context);
        }
        // 保存起来.
        $yielding = new Yielding($this->breakpoint->process()->currentThread());
        $session = $this->session;
        $session->repo->driver->saveYielding($session, $yielding);


        if (isset($context)) {
            $task = new Node($context);
            $this->breakpoint->process()->replaceThread(new Thread($task));
            return $this;
        }

        return $this->fallback();
    }

    /*------ 后退 ------*/

    public function nextStage() : ? History
    {
        $next = $this->breakpoint->process()->nextStage();
        return isset($next) ? $this : null;
    }

    /**
     * 返回到依赖当前task 的上一个task. 可能为null
     *
     * @return History|null
     */
    public function intended() : ? History
    {
        $task = $this->breakpoint->process()->intended();
        return isset($task) ? $this : null;
    }

    /**
     * 回退到上一个可用的 task.
     * -> intend -> block -> sleep -> root 的回调路径.
     *
     * @return History |null
     */
    public function fallback() : ? History
    {
        $process = $this->breakpoint->process();
        $thread = $process->wake();
        if (isset($thread)) {
            $process->replaceThread($thread);
            return $this;
        }

        return null;
    }


    /**
     * 返回到 session 的 root 节点.
     * @return History
     */
    public function home() : History
    {
        $home = $this->homeProcess();
        $this->breakpoint->replaceProcess($home);
        return $this;
    }

//    /**
//     * 如果是同一个节点, 则用上一次对话的内容.
//     * 否则不动.
//     */
//    public function repeat() : void
//    {
//        $prevBreakpoint = $this->prev();
//        if (!isset($prevBreakpoint)) {
//            return;
//        }
//
//        $prev = $this->prevBreakpoint->process()->currentTask();
//        $current = $this->breakpoint->process()->currentTask();
//        if ($prev->getContextId() == $current->getContextId() && $prev->getStage() == $current->getStage()) {
//            $this->rewind();
//        }
//    }

    /**
     * 完全当这一轮对话没有发生过.
     */
    public function rewind() : void
    {
        $prev = $this->prev();
        if (isset($prev)) {
            $this->setBreakpoint($prev);
        }
    }



    public function __get($name)
    {
        switch($name) {
            case 'breakpointArr' :
                return $this->breakpoint->toArray();
            case 'tracker' :
                return $this->tracker;
            default:
                return null;
        }
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->sessionId);
    }

}