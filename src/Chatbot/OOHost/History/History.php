<?php


namespace Commune\Chatbot\OOHost\History;


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
class History
{

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
        $this->logger = $session->logger;

        $snapshot = $this->session->repo->snapshot;
        $this->prevBreakpoint = $snapshot->breakpoint;

        //重新赋值
        $this->setBreakpoint(new Breakpoint($session, $this->prevBreakpoint));
        $this->tracker = new Tracker($session->sessionId);
    }



    public function currentTask() : Node
    {
        return $this->breakpoint
            ->process
            ->thread
            ->currentNode();
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
        $current = $this->currentTask();
        if ($reset) {
            $current->flushStacks();
        }
        $current->go($stageName);

        return $this;
    }

    public function addStage(string $stage)  : History
    {
        $this->currentTask()->add($stage);
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
        $lastId = array_pop($this->breakpoint->backtrace);

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
        $this->breakpoint->process->thread = $newThread;
        return $this;
    }

    public function replaceNodeTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $this->breakpoint->process->thread->node = new Node($context);
        return $this;
    }

    public function replaceProcessTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $process = new Process($this->session);
        $process->sleepTo(new Thread(new Node($context)));
        $this->breakpoint->process = $process;
        return $this;
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
        $this->breakpoint->process->thread->push($task);
        return $this;
    }

    /**
     * 暂停当前thread. 新thread fulfill 的时候, 可能被重新唤醒.
     * 优先级高于 sleep, 并且先入先出
     *
     * @param Context $context
     * @return History
     */
    public function sleepTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $newThread = new Thread(new Node($context));
        $this->breakpoint->process->sleepTo($newThread);
        return $this;
    }

    protected function wrapContext(Context $context = null) : Context
    {
        if (isset($context)) {
            return $context->toInstance($this->session);
        }
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
        $context = $this->wrapContext($context);
        // 保存起来.
        $yielding = new Yielding($this->breakpoint->process->thread);
        $session = $this->session;
        $session->repo->driver->saveYielding($session, $yielding);


        if (isset($context)) {
            $task = new Node($context);
            $this->breakpoint->process->thread = new Thread($task);
            return $this;
        }

        return $this->fallback();
    }

    /*------ 后退 ------*/

    public function nextStage() : ? History
    {
        $next = $this->currentTask()->next();
        return isset($next) ? $this : null;
    }

    /**
     * 返回到依赖当前task 的上一个task. 可能为null
     *
     * @return History|null
     */
    public function intended() : ? History
    {
        $task = $this->breakpoint->process->thread->pop();
        return isset($task) ? $this : null;
    }

    /**
     * 回退到上一个可用的 task.
     * -> intend -> block -> sleep -> root 的回调路径.
     *
     * @return History
     */
    public function fallback() : History
    {
        $process = $this->breakpoint->process;
        $thread = $process->pop();
        if (isset($thread)) {
            $process->thread = $thread;
            return $this;
        }

        return $this->home();
    }


    /**
     * 返回到 session 的 root 节点.
     * @return History
     */
    public function home() : History
    {
        $this->breakpoint->process = new Process($this->session);
        return $this;
    }

    /**
     * 跟repeat 不一样, 完全当这一轮对话没有发生过.
     */
    public function rewind() : void
    {
        $breakpoint = new Breakpoint($this->session, $this->prevBreakpoint);
        $this->setBreakpoint($breakpoint);
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
        if (CHATBOT_DEBUG) $this->logger->debug(__METHOD__);
    }

}