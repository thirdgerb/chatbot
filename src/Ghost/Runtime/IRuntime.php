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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Contracts\Ghost\RuntimeDriver;
use Commune\Ghost\Memory\IMemory;
use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;
use Commune\Blueprint\Exceptions\IO\SaveDataFailException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRuntime implements Runtime, Spied
{
    use SpyTrait;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var RuntimeDriver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var string
     */
    protected $sessionId;

    /*---- cached ----*/

    /**
     * @var Process|null
     */
    protected $process;

    protected $sessionMemories = [];

    protected $longTermMemories = [];

    /**
     * IRuntime constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
        if (! $this->cloner->isStateless()) {
            $this->driver = $cloner->getContainer()->make(RuntimeDriver::class);
        }

        $this->traceId = $cloner->getTraceId();
        $this->sessionId = $cloner->getSessionId();

        static::addRunningTrace($this->traceId, $this->traceId);
    }

    /*---- memory ----*/

    public function findMemory(string $id, bool $longTerm, array $defaults): Memory
    {
        return $longTerm
            ? $this->findLongTermMemory($id, $defaults)
            : $this->findSessionMemory($id, $defaults);
    }

    protected function findSessionMemory(string $id, array $defaults) : Memory
    {
        if (!isset($this->sessionMemories)) {
            $this->sessionMemories = $this->driver->fetchSessionMemories($this->sessionId);
        }

        return $this->sessionMemories[$id]
            ?? $this->sessionMemories[$id] = new IMemory($id, false, $defaults);
    }

    protected function findLongTermMemory(string $id, array $defaults) : Memory
    {
        if (isset($this->longTermMemories[$id])) {
            return $this->longTermMemories[$id];
        }

        $memory = isset($this->driver)
            ? $this->driver->findLongTermMemories($id)
            : null;

        $memory = $memory ?? new IMemory($id, true, $defaults);

        return $this->longTermMemories[$id] = $memory;
    }


    /*---- process ----*/

    public function getCurrentProcess(): Process
    {
        if (isset($this->process)) {
            return $this->process;
        }


        // 从历史记忆中寻找.
        if (!$this->cloner->isStateless()) {
            $process = $this->driver->fetchProcess($this->sessionId);
            if (isset($process)) {
                // 生成一个新的 Snapshot
                return $this->process = $process->nextSnapshot($this->cloner->getTraceId());
            }
        }

        // 创建一个新的.
        $contextName = $this->cloner->scene->contextName;
        return $this->process = $this->createProcess($contextName);
    }

    public function setCurrentProcess(Process $process): void
    {
        $this->process = $process;
    }

    public function createProcess(string $contextName): Process
    {
        $root = Ucl::create($this->cloner, $contextName);
        return new IProcess($this->sessionId, $root, $this->cloner->getTraceId());
    }

    /*------ contextMsg ------*/

    public function toContextMsg(): ? ContextMsg
    {
        if (!isset($this->process)) {
            return null;
        }

        $waiter = $this->process->waiter;
        if (empty($waiter)) {
            return null;
        }

        // prev 不存在时.
        $prev = $this->process->prev;
        $prevWaiter =isset($prev) ? $prev->waiter : null;

        $changed = !isset($prevWaiter) || ($prevWaiter->await !== $waiter->await);

        if ($changed) {
            $ucl = $this->process->decodeUcl($waiter->await);
            $context = $this->cloner->getContext($ucl);
            return $context->toContextMsg();
        }

        return null;
    }


    /*------ save ------*/

    public function save(): void
    {
        if ($this->cloner->isStateless()) {
            return;
        }

        if (!isset($this->driver)) {
            return;
        }

        if (!isset($this->process)) {
            return;
        }

        $success = false;
        $e = null;
        try {
            $expire = $this->cloner->getSessionExpire();
            $success = $this->saveLongTermMemories()
                && $this->saveSessionMemories($expire)
                && $this->cacheProcess($expire);

        } catch (\Throwable $e) {
        }

        if (!$success || isset($e)) {
            throw new SaveDataFailException(
                __METHOD__,
                $this->traceId,
                $e
            );
        }
    }

    protected function cacheProcess(int $expire) : bool
    {
        return isset($this->process)
            ? $this->driver->cacheProcess($this->sessionId, $this->process, $expire)
            : false;
    }

    protected function saveSessionMemories(int $expire) : bool
    {
        $memories = array_filter($this->sessionMemories, function(Memory $memory){
            return $memory->isChanged();
        });

        return empty($memories)
            ? true
            : $this->driver->cacheSessionMemories($this->sessionId, $memories, $expire);
    }

    protected function saveLongTermMemories() : bool
    {
        $memories = array_filter($this->longTermMemories, function(Memory $memory) {
            return $memory->isChanged();
        });

        return empty($memories)
            ? true
            : $this->driver->saveLongTermMemories(
                $this->cloner->scope,
                $memories
            );
    }


    public function __destruct()
    {
        // 清空数据
        $this->driver = null;
        $this->cloner = null;

        static::removeRunningTrace($this->traceId);
    }
}