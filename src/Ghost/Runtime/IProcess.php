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
use Commune\Framework\Spy\SpyAgency;
use Commune\Protocols\HostMsg\Convo\QA\QuestionMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IProcess implements Process, HasIdGenerator, \Serializable
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

    public static $maxYielding = 20;

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
        $this->_root = $root->encode();
        SpyAgency::incr(static::class);
    }

    public function nextSnapshot(string $id, int $maxBacktrace): Process
    {
        $next = clone $this;
        $next->_id = $id ?? $this->createUuId();
        $next->_prev = $this;
        self::$maxBacktrace = $maxBacktrace;
        return $next;
    }

    /*-------- to array --------*/

    public function toArray(): array
    {
        return [
            'belongsTo' => $this->_belongsTo,
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
                'yielding' => $this->_yielding,
            ]
        ];
    }

    /*-------- wait --------*/

    public function await(
        Ucl $ucl,
        ? QuestionMsg $question,
        array $routes
    ): void
    {
        $this->setWaiting($ucl, Context::AWAIT);
        $waiter = new IWaiter(
            $ucl->encode(),
            $question,
            $routes
        );

        if (empty($this->_waiter)) {
            $this->_waiter = $waiter;
            return;
        }

        // backtrace
        $last = $this->_waiter;
        $this->_waiter = $waiter;

        if ($last->await !== $waiter->await) {
            array_unshift($this->_backtrace, $last->await);
            ArrayUtils::maxLength($this->_backtrace, self::$maxBacktrace);
        }
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
        return $this->decode($this->_root);
    }


    /*-------- await --------*/


    public function getAwait(): ? Ucl
    {
        if (isset($this->_waiter)) {
            $await = $this->_waiter->await;
            return $this->decode($await);
        }

        return null;
    }

    public function getAwaitQuestion(): ? QuestionMsg
    {
        return isset($this->_waiter)
            ? $this->_waiter->question
            : null;
    }

    public function getAwaitRoutes(): array
    {
        if (!isset($this->_waiter)) {
            return [];
        }

        $question = $this->_waiter->question;
        $routes = isset($question)
            ? array_values($question->getRoutes())
            : [];

        foreach ($this->_waiter->routes as $routeStr) {
            $routes[] = $this->decode($routeStr);
        }

        return $routes;
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

    /*-------- yielding --------*/
    public function addYielding(Ucl $ucl): void
    {
        $this->setWaiting($ucl, Context::YIELDING);
        $id = $ucl->getContextId();
        $this->_yielding[$id] = 1;
        ArrayUtils::maxLength($this->_yielding, self::$maxYielding);
    }

    public function eachYielding(): \Generator
    {
        foreach ($this->_yielding as $id => $bool) {
            yield $this->getContextUcl($id);
        }
    }


    /*-------- block --------*/

    public function addBlocking(Ucl $ucl, int $priority): void
    {
        $this->setWaiting($ucl, Context::BLOCKING);
        $id = $ucl->getContextId();
        $this->_blocking[$id] = $priority;
        // priority 高的排前面.
        uasort($this->_blocking, function($p1, $p2) {
            return $p2 - $p1;
        });

        ArrayUtils::maxLength($this->_depending, self::$maxBlocking);
    }

    public function firstBlocking(): ? Ucl
    {
        if (empty($this->_blocking)) {
            return null;
        }

        foreach ($this->_blocking as $id => $priority) {
            return $this->getContextUcl($id);
        }

        return null;
    }

    public function eachBlocking(): \Generator
    {
        foreach ($this->_blocking as $id => $priority) {
            yield $this->getContextUcl($id);
        }
    }

    /*-------- sleep --------*/

    public function addSleeping(Ucl $ucl, array $wakenStages): void
    {
        $this->setWaiting($ucl, Context::SLEEPING);

        $id = $ucl->getContextId();
        $this->_sleeping[$id] = $wakenStages;
        ArrayUtils::maxLength($this->_sleeping, self::$maxSleeping);
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
        foreach ($this->_depending as $dependingId => $depended) {
            if ($dependedContextId === $depended) {
                $result[] = $this->getTaskById($dependingId)->getUcl();
            }
        }

        return $result;
    }

    public function dumpDepending(string $dependedContextId): array
    {
        $result = [];
        foreach ($this->_depending as $dependingId => $depended) {
            if ($dependedContextId === $depended) {
                $result[] = $this->getTaskById($dependingId)->getUcl();
                unset($this->_depending[$dependingId]);
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

        if ($step < 1) {
            return false;
        }

        $waiter = null;
        do {
            $await = array_shift($this->_backtrace);
            $step --;
        } while($step > 0 && isset($await));

        if (isset($await)) {
            $waiter = new IWaiter($await, null, []);
            $this->_waiter = $waiter;
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
        ArrayUtils::maxLength($this->_dying, self::$maxDying);
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
            $id = $this->decode($this->_waiter->await)->getContextId();
            $tasks[$id] = $this->getTaskById($id);
        }

        $root = $this->getRoot();
        $rootTask = $this->getTask($root);
        $tasks[$rootTask->getId()] = $rootTask;

        $this->_tasks = $tasks;
    }


    /*-------- methods --------*/

    protected function decode(string $str)
    {
        return $this->_decoded[$str]
            ?? $this->_decoded[$str] = Ucl::decode($str);
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

    public function serialize()
    {
        $fields = $this->__sleep();
        $results = [];
        foreach ($fields as $field) {
            $results[$field] = $this->{$field};
        }

        $str = ProcessSerializeManager::serialize($results);
        return $str;
    }

    public function unserialize($serialized)
    {
        $data = ProcessSerializeManager::unserialize($serialized);
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
    }


    public function __wakeup()
    {
    }

    public function __clone()
    {
        foreach ($this->_tasks as $id => $val) {
            $this->_tasks[$id] = clone $val;
        }

        $this->_waiter = isset($this->_waiter)
            ? clone $this->_waiter
            : null;

        foreach ($this->_backtrace as $id => $val) {
            $this->_backtrace[$id] = $val;
        }
    }


    public function __destruct()
    {
        $this->_backtrace = [];
        $this->_tasks = [];
        $this->_waiter = null;
        SpyAgency::decr(static::class);
    }

}