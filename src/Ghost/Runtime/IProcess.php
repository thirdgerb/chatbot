<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IProcess implements Process, HasIdGenerator
{
    use ArrayAbleToJson, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_belongsTo;

    /**
     * @var ITask[]
     */
    protected $_tasks = [];

    /**
     * @var string
     */
    protected $_root;

    /**
     * @var Waiter[]
     */
    protected $_backtrace = [];

    /**
     * @var Waiter|null
     */
    protected $_waiter;

    /*----- waiting -----*/

    /**
     * @var string[]
     */
    protected $_depending = [];

    /**
     * @var int[]
     */
    protected $_callbacks = [];

    /**
     * @var int[]
     */
    protected $_blocking = [];

    /**
     * @var string[][]
     */
    protected $_sleeping = [];

    /**
     * @var array
     */
    protected $_yielding = [];

    /**
     * @var array[]
     */
    protected $_dying = [];

    /*----- cached -----*/

    /**
     * @var null|Process
     */
    protected $_prev;

    /**
     * @var Ucl[]
     */
    protected $_decoded = [];

    /*----- config -----*/

    public static $maxBacktrace = 20;

    public static $maxSleeping = 20;

    public static $maxBlocking = 20;

    public static $maxDying = 20;

    /**
     * IProcess constructor.
     * @param string $belongsTo
     * @param Ucl $root
     * @param string|null $id
     */
    public function __construct(
        string $belongsTo,
        Ucl $root,
        string $id = null
    )
    {
        $this->_belongsTo = $belongsTo;
        $this->_id = $id ?? $this->createUuId();
        $this->_root = $root->toEncodedStr();
    }

    public function nextSnapshot(string $id, int $maxBacktrace): Process
    {
        $next = clone $this;
        $next->_id = $id ?? $this->createUuId();
        $next->_prev = $this;
        return $next;
    }

    /*-------- to array --------*/

    public function toArray(): array
    {
        return [
            'belongTo' => $this->_belongsTo,
            'id' => $this->_id,
            'tasks' => ArrayUtils::recursiveToArray($this->_tasks),
            'root' => $this->_root,
            'waiter' => isset($this->_waiter) ? $this->_waiter->toArray() : null,
            'backtrace' => ArrayUtils::recursiveToArray($this->_backtrace),
            'waiting' => [
                'callbacks' => $this->_callbacks,
                'depending' => $this->_depending,
                'blocking' => $this->_blocking,
                'sleeping' => $this->_sleeping,
                'dying' => $this->_dying,
            ]
        ];
    }

    /*-------- wait --------*/

    public function await(
        Ucl $ucl,
        ? QuestionMsg $question,
        array $stageRoutes,
        array $contextRoutes
    ): void
    {
        $this->setWaiting($ucl, Context::AWAIT);

        $waiter = new IWaiter(
            $ucl->toEncodedStr(),
            $question,
            $stageRoutes,
            $contextRoutes
        );

        if (empty($this->_waiter)) {
            $this->_waiter = $waiter;
            return;
        }

        // backtrace
        $last = $this->_waiter;
        $this->_waiter = $waiter;

        array_unshift($this->_backtrace, $last);
        ArrayUtils::maxLength($this->_backtrace, self::$maxBacktrace);
    }

    public function isFresh(): bool
    {
        return !isset($this->_waiter);
    }

    public function getContextUcl(string $contextId): ? Ucl
    {
        $task = $this->getTaskById($contextId);
        return isset($task)
            ? $task->getUcl()
            : null;
    }

    public function getRoot(): Ucl
    {
        return $this->decoded($this->_root);
    }


    /*-------- await --------*/


    public function getAwait(): ? Ucl
    {
        if (isset($this->_waiter)) {
            $await = $this->_waiter->await;
            return $this->decoded($await);
        }

        return null;
    }

    public function getAwaitQuestion(): ? QuestionMsg
    {
        return isset($this->_waiter)
            ? $this->_waiter->question
            : null;
    }


    public function getAwaitStageNames(): array
    {
        return isset($this->_waiter)
            ? $this->_waiter->stageRoutes
            : [];
    }

    public function getAwaitContexts(): array
    {
        $contexts = isset($this->_waiter)
            ? $this->_waiter->contextRoutes
            : [];

        return array_map(function(string $contextName) {
            return $this->decoded($contextName);
        }, $contexts);
    }

    public function getWaiter(): ? Waiter
    {
        return $this->_waiter;
    }

    /*-------- activate --------*/

    public function activate(Ucl $ucl): void
    {
        $this->setWaiting($ucl, Context::ALIVE);
    }

    /*-------- block --------*/

    public function addBlocking(Ucl $ucl, int $priority): void
    {
        $this->setWaiting($ucl, Context::BLOCKING);
        $id = $ucl->getContextId();
        $this->_depending[$id] = $priority;
    }

    public function firstBlocking(): ? Ucl
    {
        if (empty($this->_depending)) {
            return null;
        }

        foreach ($this->_depending as $id => $priority) {
            return $this->getContextUcl($id);
        }

        return null;
    }

    public function eachBlocking(): \Generator
    {
        foreach ($this->_depending as $id => $priority) {
            yield $this->getContextUcl($id);
        }
    }

    /*-------- sleep --------*/

    public function addSleeping(Ucl $ucl, array $wakenStages): void
    {
        $this->setWaiting($ucl, Context::SLEEPING);

        $id = $ucl->getContextId();

        $this->_sleeping[$id] = $wakenStages;
    }

    public function firstSleeping(): ? Ucl
    {
        if (empty($this->_sleeping)) {
            return null;
        }

        foreach ($this->_sleeping as $id => $stages) {
            return $this->getContextUcl($id);
        }

        return null;
    }

    public function eachSleeping(): \Generator
    {
        foreach ($this->_sleeping as $id => $stages) {
            yield $this->getContextUcl($id);
        }
    }

    /*-------- depends --------*/

    public function getDepended(string $contextId): ? Ucl
    {
        if (isset($this->_depending[$contextId])) {
            $id = $this->_depending[$contextId];
            return $this->getContextUcl($id);
        }

        return null;
    }

    public function addDepending(Ucl $ucl, string $dependedContextId): void
    {
        $this->setWaiting($ucl, Context::DEPENDING);

        $id = $ucl->getContextId();
        $this->_depending[$id] = $dependedContextId;
    }

    public function getDepending(string $dependedContextId): array
    {
        $result = [];
        foreach ($this->_depending as $dependingId => $dependedContextId) {
            if ($dependedContextId === $dependedContextId) {
                $result[] = $dependingId;
            }
        }

        return $result;
    }

    /*-------- callbacks --------*/

    public function addCallback(Ucl ...$callbacks): void
    {
        foreach ($callbacks as $callback) {
            $id = $callback->getContextId();
            $this->setWaiting($callback, Context::CALLBACK);
            $this->_callbacks[$id] = 1;
        }
    }

    public function firstCallback(): ? Ucl
    {
        foreach ($this->_callbacks as $id => $val) {
            return $this->getContextUcl($id);
        }

        return null;
    }

    public function eachCallbacks(): \Generator
    {
        foreach ($this->_callbacks as $id => $val) {
            yield $this->getContextUcl($id);
        }
    }

    /*-------- back step --------*/

    public function backStep(int $step): bool
    {
        $waiter = null;

        do {
            $now = $waiter;
            $waiter = array_shift($this->_backtrace);
            $step --;
        } while($step > 0 && $waiter);

        if (isset($now)) {
            $this->_waiter = $now;
            return true;
        }

        return false;
    }

    /*-------- dying --------*/

    public function addDying(Ucl $ucl, int $turns = 0, array $restoreStages = [])
    {
        $this->setWaiting($ucl, Context::DYING);
        $contextId = $ucl->getContextId();
        $this->_dying[$contextId] = [$turns, $restoreStages];
    }

    public function gc(): void
    {
        $dying = [];
        foreach ($this->_dying as $id => list($turns, $restoreStages)) {
            $turns --;
            if ($turns >= 0) {
                $dying[$id] = [$turns, $restoreStages];
            }
        }
        $this->_dying = $dying;

        $tasks = [];

        foreach ($this->_callbacks as $id => $val) {
            $tasks[$id] = $this->getTaskById($id);
        }

        foreach ($this->_blocking as $id => $val) {
            $tasks[$id] = $this->getTaskById($id);
        }

        foreach ($this->_sleeping as $id => $val) {
            $tasks[$id] = $this->getTaskById($id);
        }

        foreach ($this->_yielding as $id => $val ) {
            $tasks[$id] = $this->getTaskById($id);
        }

        foreach ($this->_depending as $id => $val) {
            $tasks[$id] = $this->getTaskById($id);
        }

        foreach ($this->_dying as $id => $val) {
            $tasks[$id] = $this->getTaskById($id);
        }

        if (isset($this->_waiter)) {
            $id = $this->decoded($this->_waiter->await)->getContextId();
            $tasks[$id] = $this->getTaskById($id);
        }

        $id = $this->getRoot()->getContextId();
        $tasks[$id] = $this->getTaskById($id);

        $this->_tasks = $tasks;
    }


    /*-------- methods --------*/

    protected function decoded(string $str)
    {
        return $this->_decoded[$str]
            ?? $this->_decoded[$str] = Ucl::decodeUclStr($str);
    }


    /*-------- task --------*/

    /**
     * @param Ucl $ucl
     * @return ITask
     */
    public function getTask(Ucl $ucl): Task
    {
        $id = $ucl->getContextId();

        return $this->_tasks[$id]
            ?? $this->_tasks[$id] = new ITask($ucl);
    }

    public function getTaskById(string $id) : ? Task
    {
        return $this->_tasks[$id] ?? null;
    }

    /*-------- waiting --------*/

    public function setWaiting(Ucl $ucl, int $status): void
    {
        $task = $this->getTask($ucl);
        $task->setStatus($ucl, $status);

        $id = $ucl->getContextId();

        unset($this->_depending[$id]);
        unset($this->_callbacks[$id]);
        unset($this->_blocking[$id]);
        unset($this->_sleeping[$id]);
        unset($this->_yielding[$id]);
        unset($this->_dying[$id]);
    }

    public function flushWaiting(): void
    {
        $this->_depending = [];
        $this->_callbacks = [];
        $this->_blocking = [];
        $this->_sleeping = [];
        $this->_yielding = [];
        $this->_dying = [];
    }

    /*-------- magic --------*/

    public function __get($name)
    {
        $name = "_$name";

        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return null;
    }

    public function __isset($name)
    {
        $val = $this->__get($name);
        return isset($val);
    }

    public function __sleep()
    {
        return [
            '_id',
            '_belongsTo',
            '_tasks',
            '_root',
            '_waiter',
            '_backtrace',
            '_callbacks',
            '_blocking',
            '_sleeping',
            '_depending',
            '_yielding',
            '_dying',
        ];
    }

    public function __wakeup()
    {
    }

    public function __clone()
    {
        foreach ($this->_tasks as $id => $val) {
            $this->_tasks[$id] = clone $val;
        }

        $this->_waiter = clone $this->_waiter;

        foreach ($this->_backtrace as $id => $val) {
            $this->_backtrace[$id] = clone $val;
        }
    }


    public function __destruct()
    {
        $this->_backtrace = [];
        $this->_tasks = [];
        $this->_waiter = null;
    }

}