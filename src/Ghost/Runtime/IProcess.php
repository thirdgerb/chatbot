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

use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\RoutesMap;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IProcess implements Process, HasIdGenerator
{
    use ArrayAbleToJson, IdGeneratorHelper;

    /*------ saving ------*/

    /**
     * @var string
     */
    protected $_sessionId;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_root;

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
     * @var array
     */
    protected $_yielding = [];

    /**
     * @var array
     */
    protected $_dying = [];

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
     * @var array
     */
    protected $canceling = [];

    /**
     * IProcess constructor.
     * @param string $_sessionId
     * @param string $_root
     * @param string|null $_id
     */
    public function __construct(string $_sessionId,string  $_root, string $_id = null)
    {
        $this->_sessionId = $_sessionId;
        $this->_id = $_id ?? $this->createUuId();
        $this->_root = $_root;
    }

    public function toArray(): array
    {
        $fields = $this->__sleep();
        $data = [];
        foreach ($fields as $field) {
            if ($field !== '_prev' || $field !== '_waiter') {
                $data[$field] = $this->{$field};
            }
        }

        $data['await'] = $this->awaiting;
        return $data;
    }

    /*------ build ------*/

    public function buildRoutes(): RoutesMap
    {
        return new IRoutesMap($this);
    }

    /*------ await ------*/

    public function setAwait(Waiter $waiter): void
    {
        if (isset($this->_waiter)) {
            array_unshift($this->_backtrace, $this->_waiter);
        }
        $this->_waiter = $waiter;
    }


    /*------ history ------*/

    public function nextSnapshot(string $id): Process
    {
        $next = clone $this;
        $next->_id = $id ?? $this->createUuId();
        $next->_prev = $this;
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

    /*------ root ------*/

    public function replaceRoot(Ucl $ucl): void
    {
        $this->_root = $ucl->toEncodedUcl();
    }

    /*------ dying ------*/

    public function addDying(Ucl $ucl, int $turns, array $restoreStages)
    {
        $str = $ucl->toEncodedUcl();
        $this->unsetWaiting($str);
        $this->_dying[$str] = [$turns, $restoreStages];
    }

    public function gc(): void
    {
        $dying = [];
        foreach ($this->_dying as $key => list($turns, $restoreStages)) {
            $turns--;
            if ($turns > 0) {
                $dying[$key] = [$turns, $restoreStages];
            }
        }

        $this->_dying = $dying;
    }


    /*------ blocking ------*/

    public function addBlocking(Ucl $ucl, int $priority): void
    {
        $str = $ucl->toEncodedUcl();
        $this->_blocking[$str] = $priority;
        sort($this->_blocking);
    }

    public function popBlocking(string $ucl = null): ? string
    {
        if (empty($this->_blocking)) {
            return null;
        }

        $popped = null;
        foreach ($this->_blocking as $key => $val) {
            $popped = $key;
        }
        unset($this->_blocking[$popped]);
        return $popped;
    }


    /*------ watching ------*/

    public function addWatcher(Ucl $watcher): void
    {
        $str = $watcher->toEncodedUcl();
        $this->unsetWaiting($str);
        $this->_watching[$str] = '';
    }

    public function popWatcher(): ? string
    {
        if (empty($this->_watching)) {
            return null;
        }
        $popped = null;
        foreach ($this->_watching as $key => $str) {
            $popped = $key;
            break;
        }
        unset($this->_watching[$popped]);
        return $popped;
    }


    /*------ sleeping ------*/

    public function addSleeping(Ucl $ucl, array $wakenStages): void
    {
        $str = $ucl->toEncodedUcl();
        $this->unsetWaiting($str);
        $this->_sleeping[$str] = $wakenStages;
    }

    public function popSleeping(string $id = null): ? string
    {
        if (empty($this->_sleeping)) {
            return null;
        }

        $pop = null;
        foreach ($this->_sleeping as $key => $stages) {
            $pop = $key;
            break;
        }

        unset($this->_sleeping[$pop]);
        return $pop;
    }


    /*------ depending ------*/

    public function addDepending(string $ucl, string $contextId): void
    {
        $this->unsetWaiting($ucl);
        $this->_depending[$ucl] = $contextId;
    }

    public function getDepending(string $contextId): array
    {
        $depending = array_reverse($this->_depending);
        return $depending[$contextId] ?? [];
    }


    /*------ unset ------*/

    public function unsetWaiting(string $ucl): void
    {
        unset($this->_depending[$ucl]);
        unset($this->_blocking[$ucl]);
        unset($this->_sleeping[$ucl]);
        unset($this->_watching[$ucl]);
        unset($this->_dying[$ucl]);
        unset($this->canceling[$ucl]);
    }

    public function flushWaiting()
    {
        $this->_depending = [];
        $this->_sleeping = [];
        $this->_watching = [];
        $this->_dying = [];
        $this->canceling = [];

        // yielding blocking 要保留.
    }

    /*------ canceling ------*/

    public function addCanceling(array $canceling): void
    {
        foreach ($canceling as $cancelingId) {
            $contextId = $this->_depending[$cancelingId];
            unset($this->_depending[$cancelingId]);
            $this->canceling[$cancelingId] = $contextId;
        }
    }

    public function popCanceling(): ? string
    {
        if (empty($this->canceling)) {
            return null;
        }

        $popped = null;
        foreach ($this->canceling as $ucl => $contextId) {
            $popped = $ucl;
            break;
        }
        unset($this->canceling[$popped]);
        return $popped;
    }


    /*------ tools ------*/

    public function decodeUcl(string $ucl): ? Ucl
    {
        return $this->decodedUcl[$ucl]
            ?? $this->decodedUcl[$ucl] = Ucl::decodeUcl($ucl);
    }


    /*------ magic ------*/


    public function __get($name)
    {
        if ($name === 'await') {
            return isset($this->_waiter)
                ? $this->_waiter->await
                : null;
        }

        if (property_exists($this, $p = "_$name")) {
            return $this->{$p};
        }

        return null;
    }

    public function __sleep()
    {
        // canceling
        $this->_depending = $this->_depending + $this->canceling;

        return [
            '_id',
            '_sessionId',
            '_root',
            '_waiter',
            '_backtrace',
            '_watching',
            '_blocking',
            '_sleeping',
            '_depending',
            '_dying',
        ];
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
