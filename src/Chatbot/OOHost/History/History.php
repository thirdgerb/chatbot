<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Exceptions\DataNotFoundException;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Commune\Chatbot\OOHost\Session\Snapshot;
use Psr\Log\LoggerInterface;

/**
 * @property-read array $breakpointArr
 * @property-read string $belongsTo
 * @property-read Snapshot $snapshot
 */
class History implements RunningSpy
{
    use RunningSpyTrait;

    /**
     * @var string
     */
    protected $belongsTo;

    /**
     * @var Snapshot
     */
    protected $snapshot;

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
     * @var callable
     */
    protected $rootContextMaker;

    public function __construct(
        Session $session,
        string $belongsTo,
        callable $rootContextMaker
    )
    {
        $this->session = $session;
        $this->belongsTo = $belongsTo;
        $this->rootContextMaker = $rootContextMaker;

        $this->sessionId = $sessionId = $session->sessionId;
        $this->logger = $session->logger;

        // sneaky 状态下, 不从缓存里读取 snapshot. 不需要 IO 开销
        $snapshot = $this->session->isSneaky()
            ? new Snapshot($this->sessionId, $belongsTo)
            : $this->session->repo->getSnapshot($sessionId, $belongsTo);

        $this->snapshot = $this->pushBreakpoint($snapshot);

        //重新赋值
        $this->tracker = new Tracker($session->sessionId);

        static::addRunningTrace($this->belongsTo, $this->belongsTo);
    }

    protected function pushBreakpoint(Snapshot $snapshot) : Snapshot
    {
        $max = $this->session->chatbotConfig->host->maxBreakpointHistory;

        $prev = $snapshot->breakpoint;

        if (isset($prev)) {
            $process = clone $prev->process();

            $backtrace = $snapshot->backtrace;
            $last = $snapshot->prevBreakpoint;
            if (isset($last)) {
                array_unshift($backtrace, $last);
                if (count($backtrace) > $max) {
                    array_pop($backtrace);
                }
            }

        } else {
            $backtrace = [];
            $process = $this->homeProcess();
        }

        $snapshot->prevBreakpoint = $prev;
        $snapshot->backtrace = $backtrace;
        $snapshot->breakpoint = new Breakpoint(
            $this->session->conversation->getConversationId(),
            $this->sessionId,
            $process
        );

        return $snapshot;
    }

    /**
     * 刷新掉当前 history 的 snapshot
     */
    public function refresh() : void
    {
        $this->session
            ->repo
            ->clearSnapshot($this->sessionId, $this->belongsTo);

        $snapshot = $this->session
            ->repo
            ->getSnapshot($this->sessionId, $this->belongsTo, true);

        $this->snapshot = $this->pushBreakpoint($snapshot);
    }


    /**
     * 重置当前 snapshot
     */
    public function rewind() : void
    {
        $prev = $this->snapshot->prevBreakpoint;
        if (!isset($prev)) {
            return;
        }
        $this->snapshot->breakpoint = $prev;
        $this->snapshot->prevBreakpoint = array_shift($this->snapshot->backtrace);
    }

    public function currentTask() : Node
    {
        return $this->snapshot->breakpoint->process()->currentTask();
    }

    public function currentQuestion() : ? Question
    {
        return $this->snapshot->breakpoint->process()->currentQuestion();
    }

    public function setQuestion(Question $question = null) : void
    {
        $this->snapshot->breakpoint->process()->setQuestion($question);
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
                $this->session,
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
        return $this->snapshot->breakpoint;
    }

    /*------ stage ------*/

    public function goStage(string $stageName, bool $reset) : History
    {
        $this->snapshot->breakpoint->process()->goStage($stageName, $reset);
        return $this;
    }

    public function addStage(string $stage)  : History
    {
        $this->snapshot->breakpoint->process()->addStage($stage);
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
        $last = array_shift($this->snapshot->backtrace);
        if (!isset($last)) {
            return null;
        }
        $this->snapshot->breakpoint = $last;
        $this->snapshot->prevBreakpoint = null;

        return $this;
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
        $this->snapshot->breakpoint->process()->replaceThread($newThread);
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
        $this->snapshot->breakpoint->process()->replaceNode(new Node($context));
        return $this;
    }

    public function replaceProcessTo(Context $context) : History
    {
        $context = $this->wrapContext($context);
        $process = $this->homeProcess();
        $process->sleepTo(new Thread(new Node($context)));
        $this->snapshot->breakpoint->replaceProcess($process);
        return $this;
    }

    protected function homeProcess() : Process
    {
        /**
         * @var Context $context
         */
        $context =call_user_func($this->rootContextMaker);

        if (!$context instanceof Context) {
            throw new RuntimeException("history root context make do not return context object . sessionId: {$this->sessionId}; belongsTo: {$this->belongsTo}");
        }

        if (!$context->isInstanced()) {
            $context = $context->toInstance($this->session);
        }

        return new Process(
            $this->session->sessionId,
            new Thread(new Node($context))
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
        $this->snapshot->breakpoint->process()->dependOn($task);
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
            $this->snapshot->breakpoint->process()->sleepTo($newThread);
            return $this;
        }

        $target = $this->snapshot->breakpoint->process()->wake();
        if (!empty($target)) {
            $this->snapshot->breakpoint->process()->sleepTo($target);
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
        $yielding = new Yielding($this->snapshot->breakpoint->process()->currentThread());
        $session = $this->session;
        $session->repo->getDriver()->saveYielding($session, $yielding);


        if (isset($context)) {
            $task = new Node($context);
            $this->snapshot->breakpoint->process()->replaceThread(new Thread($task));
            return $this;
        }

        return $this->fallback();
    }

    /*------ 后退 ------*/

    public function nextStage() : ? History
    {
        $next = $this->snapshot->breakpoint->process()->nextStage();
        return isset($next) ? $this : null;
    }

    /**
     * 返回到依赖当前task 的上一个task. 可能为null
     *
     * @return History|null
     */
    public function intended() : ? History
    {
        $task = $this->snapshot->breakpoint->process()->intended();
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
        $process = $this->snapshot->breakpoint->process();
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
        $this->snapshot->breakpoint->replaceProcess($home);
        return $this;
    }

    public function __get($name)
    {
        switch($name) {
            case 'breakpointArr' :
                return $this->snapshot->breakpoint->toArray();
            case 'belongsTo' :
                return $this->belongsTo;
            case 'snapshot' :
                return $this->snapshot;
            default:
                return null;
        }
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->belongsTo);
    }

}