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
use Commune\Blueprint\Ghost\Runtime\RoutesMap;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IProcessBak2 implements Process, HasIdGenerator
{
    use ArrayAbleToJson, IdGeneratorHelper;

    /*------ properties ------*/

    /**
     * @var string
     */
    protected $_belongsTo;


    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_root;

    protected $_contexts = [];

    /**
     * @var Waiter|null
     */
    protected $_waiter;

    /**
     * @var Waiter[]
     */
    protected $_backtrace = [];

    /**
     * @var array
     */
    protected $_watching = [];

    /**
     * @var array
     */
    protected $_blocking = [];

    /**
     * @var array
     */
    protected $_sleeping = [];

    /**
     * @var array
     */
    protected $_depending = [];

    /**
     * @var null|array
     */
    protected $_dependingMap;

    /**
     * @var array
     */
    protected $_yielding = [];

    /**
     * @var array
     */
    protected $_dying = [];

    protected $_callbacks = [];

    protected $_canceling = [];



    /*------ temporary ------*/

    /**
     * @var Process|null
     */
    protected $_prev;

    /**
     * @var Ucl[]
     */
    protected $decodedUcl = [];

    /**
     * IProcess constructor.
     * @param string $belongsTo
     * @param Ucl $root
     * @param string|null $id
     */
    public function __construct(string $belongsTo, Ucl $root, string $id = null)
    {
        $this->_belongsTo = $belongsTo;
        $this->_id = $id ?? $this->createUuId();


        $this->_root = $root->getContextId();
    }

    public function toArray(): array
    {
        // todo
    }

    /*------ build ------*/

    public function buildRoutes(): RoutesMap
    {
        return new IRoutesMap($this);
    }

    /*------ await ------*/

    public function await(Ucl $ucl, Waiter $waiter): void
    {
        $this->unsetWaiting($ucl);
        $this->setContext($ucl, Context::AWAIT);


        if (isset($this->_waiter)) {
            array_unshift($this->_backtrace, $this->_waiter);
        }
        $this->_waiter = $waiter;
    }

    /*------ status ------*/

    public function isContextStatus(string $contextId, int $status): bool
    {
        $actual = $this->getContextStatus($contextId);

        return $status & $actual > 0;
    }

    public function getContextStatus(string $contextId): int
    {
        return $this->_contexts[$contextId][1];
    }


    /*------ contexts ------*/

    protected function setContext(Ucl $ucl, int $status, array $path = null) : void
    {
        $id = $ucl->getContextId();
        if (array_key_exists($id, $this->_contexts)) {
            $path = $path ?? $this->_contexts[$id][1] ?? [];
            $this->_contexts[$id]  = [$ucl->toEncodedStr(), $status, $path];

        } else {
            $this->_contexts[$id] = [$ucl->toEncodedStr(), $status, []];
        }
    }

    protected function getContextUcl(string $id) : Ucl
    {
        return $this->decodeUcl($this->_contexts[$id][0]);
    }

    protected function hasContext(string $contextId) : bool
    {
        return array_key_exists($contextId, $this->_contexts);
    }

    protected function unsetContext(string $contextId) : void
    {
        unset($this->_contexts[$contextId]);
    }

    /*------ history ------*/

    public function nextSnapshot(string $id, int $maxBacktrace): Process
    {
        $next = clone $this;
        $next->_id = $id ?? $this->createUuId();
        $next->_prev = $this;

        while(count($this->_backtrace) > $maxBacktrace) {
            array_pop($this->_backtrace);
        }

        return $next;
    }


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

    /*------ dying ------*/

    public function addDying(Ucl $ucl, int $turns, array $restoreStages)
    {
        $this->unsetWaiting($ucl);
        $this->setContext($ucl, Context::DYING);

        $id = $ucl->getContextId();
        $this->_dying[$id] = [$turns, $restoreStages];
    }

    public function gc(): void
    {
        $dying = [];
        foreach ($this->_dying as $contextId => list($turns, $restoreStages)) {
            $turns--;
            if ($turns > 0) {
                $dying[$contextId] = [$turns, $restoreStages];
            }
        }

        $this->_dying = $dying;
        $this->removeNonWaitingContext();
    }

    protected function removeNonWaitingContext() : void
    {
        $contexts = [];

        foreach ($this->_depending as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_callbacks as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_blocking as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_sleeping as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_watching as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_yielding as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        foreach ($this->_dying as $id => $val) {
            $contexts[$id] = $this->_contexts[$id];
        }

        $this->_contexts = $contexts;
    }


    /*------ blocking ------*/

    public function addBlocking(Ucl $ucl, int $priority): void
    {
        $this->unsetWaiting($ucl);
        $this->setContext($ucl, Context::BLOCKING);

        $id = $ucl->getContextId();
        $this->_blocking[$id] = $priority;
        sort($this->_blocking);
    }

    public function popBlocking(string $contextId = null): ? Ucl
    {
        if (empty($this->_blocking)) {
            return null;
        }

        $popped = null;
        foreach ($this->_blocking as $key => $val) {
            $popped = $key;
            break;
        }

        unset($this->_blocking[$popped]);
        return $this->getContextUcl($popped);
    }

    public function countBlocking() : int
    {
        return count($this->_blocking);
    }

    /**
     * @return Ucl[]
     */
    public function getBlocking() : array
    {
        $ids = array_keys($this->_blocking);
        $results = [];

        foreach ($ids as $id) {
            $results[$id] = $this->getContextUcl($id);
        }

        return $results;
    }

    /*------ sleeping ------*/

    public function addSleeping(Ucl $ucl, array $wakenStages): void
    {
        $this->unsetWaiting($ucl);
        $this->setContext($ucl, Context::SLEEPING);

        $id = $ucl->getContextId();

        $this->_sleeping[$id] = $wakenStages;
    }

    public function popSleeping(string $id = null): ? Ucl
    {
        if (empty($this->_sleeping)) {
            return null;
        }

        $pop = null;
        foreach ($this->_sleeping as $id => $stages) {
            $pop = $id;
            break;
        }

        unset($this->_sleeping[$pop]);
        return $this->getContextUcl($pop);
    }

    public function getSleeping() : array
    {
        $ids = array_keys($this->_sleeping);

        $result = [];
        foreach ($ids as $id) {
            $result[$id] = $this->getContextUcl($id);
        }

        return $result;
    }

    /*------ depending ------*/

    public function addDepending(Ucl $ucl, string $dependedContextId): void
    {
        $this->unsetWaiting($ucl);
        $this->setContext($ucl, Context::DEPENDING);

        $id = $ucl->getContextId();

        $this->_depending[$id] = $dependedContextId;
        $this->_dependingMap = null;
    }

    public function dumpDepending(string $dependedContextId): array
    {
        $ids = $this->getDependingOn($dependedContextId);
        foreach ($ids as $id) {
            unset($this->_depending[$id]);
        }

        $this->_dependingMap = null;
        return $ids;
    }

    protected function getDependingMap() : array
    {
        if (isset($this->_dependingMap)) {
            return $this->_dependingMap;
        }

        $map = [];
        foreach ($this->_depending as $id => $depended) {
            $map[$depended][] = $id;
        }
        return $this->_dependingMap = $map;
    }

    public function getDependingOn(string $dependedContextId): array
    {
        $map = $this->getDependingMap();
        return $map[$dependedContextId] ?? [];
    }


    /*------ path ------*/

    public function resetPath(Ucl $ucl, array $path = []): void
    {
        $id = $ucl->getContextId();
        if (isset($this->_contexts[$id])) {
            $this->_contexts[$id][2] = $path;
        }
    }

    protected function getContextPath(string $contextId) : array
    {
        return $this->_contexts[$contextId][2] ?? [];
    }

    public function insertPath(Ucl $ucl, array $path): void
    {
        $id = $ucl->getContextId();
        $existsPath = $this->getContextPath($id);
        $path = array_merge($path, $existsPath);
        $this->resetPath($ucl, $path);
    }

    public function shiftPath(string $contextId): ? string
    {
        if (isset($this->_contexts[$contextId])) {
            $path = $this->_contexts[$contextId][2];
            $shift = array_shift($path);
            $this->_contexts[$contextId][2] = $path;
            return $shift;
        }

        return null;
    }

    public function pathExists(string $contextId): bool
    {
        return !empty($this->_contexts[$contextId][2]);
    }

    /*------ callback ------*/

    public function popCallback(): ? Ucl
    {
        if (empty($this->_callbacks)) {
            return null;
        }

        $pop = null;
        foreach ($this->_callbacks as $id => $val) {
            $pop = $id;
            break;
        }

        return $this->getContextUcl($pop);
    }

    public function addCallback(Ucl ...$callbacks): void
    {
        foreach ($callbacks as $callback) {
            $this->unsetWaiting($callback);
            $this->setContext($callback, Context::CALLBACK);
            $this->_callbacks[$callback->getContextId()] = 1;
        }
    }

    /*------ canceling ------*/

    /**
     * @param Ucl[] $canceling
     */
    public function addCanceling(array $canceling): void
    {
        foreach ($canceling as $ucl) {
            $id = $ucl->getContextId();
            $this->unsetWaiting($ucl);
            $this->setContext($ucl, Context::CANCELING);
            $this->_canceling[$id] = 1;
        }
    }

    public function popCanceling(): ? Ucl
    {
        if (empty($this->_canceling)) {
            return null;
        }

        $pop = null;
        foreach ($this->_canceling as $id => $val) {
            $pop = $id;
            break;
        }

        unset($this->_canceling[$pop]);
        return $this->getContextUcl($pop);
    }

    public function dumpCanceling(): array
    {
        if (empty($this->_canceling)) {
            return [];
        }

        $ids = array_keys($this->_canceling);
        $this->_canceling = [];

        return array_map(function($id){
            return $this->getContextUcl($id);
        }, $ids);
    }

    /*------ unset ------*/



    /*------ unset ------*/

    public function unsetWaiting(Ucl $ucl): void
    {
        $id = $ucl->getContextId();

        unset($this->_depending[$id]);
        unset($this->_callbacks[$id]);
        unset($this->_blocking[$id]);
        unset($this->_sleeping[$id]);
        unset($this->_yielding[$id]);
        unset($this->_watching[$id]);
        unset($this->_dying[$id]);
        unset($this->_canceling[$id]);
    }

    public function flushWaiting()
    {

        //todo
        // yielding blocking 要保留.
    }


    /*------ tools ------*/

    protected function decodeUcl(string $ucl): Ucl
    {
        if (isset($this->decodedUcl[$ucl])) {
            return $this->decodedUcl[$ucl];
        }

        $decoded = Ucl::decodeUclStr($ucl);

        if (!$decoded->isValidPattern()) {
            throw new InvalidArgumentException("invalid ucl pattern of $ucl");
        }

        return $this->decodedUcl[$ucl]
            ?? $this->decodedUcl[$ucl] = $decoded;
    }


    /*------ magic ------*/


    public function __get($name)
    {
        // todo

    }

    public function __isset($name)
    {
        // todo
    }

    public function __sleep()
    {
        // todo
    }

    public function __wakeup()
    {
        // todo
    }

    public function __clone()
    {
        $this->_waiter = clone $this->_waiter;
    }

    public function __destruct()
    {
        $this->_prev = null;

        // decoded
        $this->decodedUcl = null;

        // backtrace
        $this->_backtrace = [];

    }
}
