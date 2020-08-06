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

use Commune\Blueprint\Exceptions\IO\LoadDataException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Runtime\Trace;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Contracts\Ghost\RuntimeDriver;
use Commune\Framework\Spy\SpyAgency;
use Commune\Ghost\Memory\IMemory;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Blueprint\Exceptions\IO\SaveDataException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRuntime implements Runtime
{
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

    /*---- cached ----*/

    /**
     * @var Process|null
     */
    protected $process;

    /**
     * @var Process|null
     */
    protected $prev;

    /**
     * @var array
     */
    protected $sessionMemories = [];

    /**
     * @var array
     */
    protected $longTermMemories = [];

    /**
     * @var Trace|null
     */
    protected $trace;

    /**
     * @var Context[]
     */
    protected $contexts = [];

    /**
     * IRuntime constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;

        // 无状态请求, 不生成 runtime driver
        if (! $this->cloner->isStateless()) {
            $this->driver = $cloner
                ->getContainer()
                ->make(RuntimeDriver::class);
        }

        $this->traceId = $cloner->getTraceId();
        SpyAgency::incr(static::class);
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
        if (isset($this->sessionMemories[$id])) {
            return $this->sessionMemories[$id];
        }

        // 不存在则生成
        return $this->sessionMemories[$id] = $this->ioFindSessionMemory($id)
            ?? new IMemory($id, false, $defaults);
    }

    protected function findLongTermMemory(string $id, array $defaults) : Memory
    {
        if (isset($this->longTermMemories[$id])) {
            return $this->longTermMemories[$id];
        }

        // 一个一个读取
        $memory = $this->ioFindLongTermMemory($id);
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

            $process = $this->ioFetchCurrentProcess();
            if (isset($process)) {
                // 生成一个新的 Snapshot
                $this->prev = $process;
                $this->process = $process
                    ->nextSnapshot(
                        $this->cloner->getTraceId(),
                        $this->cloner->config->maxBacktrace
                    );

                return $this->process;
            }
        }

        // 创建一个新的.
        $root = $this->cloner->scene->entry;
        return $this->process = $this->createProcess($root);
    }

    public function setCurrentProcess(Process $process): void
    {
        $this->process = $process;
    }

    public function createProcess(Ucl $root): Process
    {
        return $this->process = new IProcess(
            $this->cloner->getConversationId(),
            $root,
            $this->cloner->input->getMessageId()
        );
    }

    /*------ context ------*/
    public function cacheContext(Context $context): void
    {
        $this->contexts[$context->getId()] = $context;
    }

    public function getCachedContext(string $id): ? Context
    {
        return $this->contexts[$id] ?? null;
    }


    /*------ contextMsg ------*/

    public function toChangedContextMsg(): ? ContextMsg
    {
        if (!isset($this->process)) {
            return null;
        }

        $waiter = $this->process->waiter;
        if (empty($waiter)) {
            return null;
        }

        // prev 不存在时.
        $prev = $this->prev;
        $prevWaiter = isset($prev) ? $prev->waiter : null;

        // 两个 await 不一致.
        $changed = !isset($prevWaiter) // 上一帧的 waiter 不存在.
            || ($prevWaiter->await !== $waiter->await); // 两个帧的 await 一致.


        // 当前的 context 数据变更过.
        $changed = $changed || $this->process
                ->getAwait()
                ->findContext($this->cloner)
                ->isChanged();

        if ($changed) {
            $ucl = $this->process->getAwait();
            $ucl = $ucl->toInstance($this->cloner);
            $context = $ucl->findContext($this->cloner);

            return $context->toContextMsg();
        }

        return null;
    }

    public function flush(): void
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

        $success = $this->ioSaveLongTermMemories()
            && $this->ioCacheProcess(0)
            && $this->ioSaveSessionMemories(0);

        if (empty($success)) {
            throw new SaveDataException('save cloner session data failed');
        }
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

        // gc, 不过现在简单了, 只要删除掉 dying 就足够了.
        $this->process->gc();

        // Context 调度各自的存储方案.
        foreach ($this->contexts as $context) {
            if ($context->isChanged()) {
                $context->save();
            }
        }

        $expire = $this->cloner->getSessionExpire();
        $success = $this->ioSaveLongTermMemories()
            && $this->ioSaveSessionMemories($expire)
            && $this->ioCacheProcess($expire);

        if (empty($success)) {
            throw new SaveDataException('save cloner session data failed');
        }
    }

    /*------ io ------*/

    protected function ioFetchCurrentProcess() : ? Process
    {
        try {
            if (!isset($this->driver)) {
                return null;
            }

            return $this->driver->fetchProcess(
                $this->cloner->getSessionId(),
                $this->cloner->getConversationId()
            );

        } catch (\Exception $e) {
            throw new LoadDataException('current process', $e);
        }
    }

    protected function ioCacheProcess(int $expire) : bool
    {
        if (!isset($this->process)) {
            return true;
        }

        if (!isset($this->driver)) {
            return true;
        }

        try {

            return $this->driver->cacheProcess(
                $this->cloner->getSessionId(),
                $this->cloner->getConversationId(),
                $this->process,
                $expire
            );
        } catch (\Exception $e) {
            throw new SaveDataException('save process data failed', $e);
        }

    }

    protected function ioSaveSessionMemories(int $expire) : bool
    {

        if (!isset($this->driver)) {
            return true;
        }

        $memories = array_filter($this->sessionMemories, function(Memory $memory){
            return $memory->isChanged();
        });


        if (empty($memories)) {
            return true;
        }

        try {
            return $this->driver->cacheSessionMemories(
                $this->cloner->getSessionId(),
                $this->cloner->getConversationId(),
                $memories,
                $expire
            );

        } catch (\Exception $e) {
            throw new SaveDataException('save session memories failed', $e);
        }
    }

    protected function ioFindSessionMemory(string $id) : ? Memory
    {
        if (!isset($this->driver)) {
            return null;
        }

        try {

            return $this
                ->driver
                ->fetchSessionMemory(
                    $this->cloner->getSessionId(),
                    $this->cloner->getConversationId(),
                    $id
                );

        } catch (\Exception $e) {
            throw new LoadDataException('load session memories failed', $e);
        }
    }

    protected function ioSaveLongTermMemories() : bool
    {
        if (!isset($this->driver)) {
            return true;
        }

        $memories = array_filter($this->longTermMemories, function(Memory $memory) {
            return $memory->isChanged();
        });

        if (empty($memories)) {
            return true;
        }

        try {
            return $this->driver->saveLongTermMemories(
                $this->cloner->getSessionId(),
                $memories
            );

        } catch (\Exception $e) {
            throw new SaveDataException('save long term memory failed', $e);
        }

    }

    protected function ioFindLongTermMemory(string $id) : ? Memory
    {
        if (!isset($this->driver)) {
            return null;
        }

        try {
            return $this->driver->findLongTermMemories(
                $this->cloner->getSessionId(),
                $id
            );

        // 请求级别的影响.
        } catch (\Exception $e) {
            throw new LoadDataException('long term memory', $e);
        }
    }

    public function __get($name)
    {
        if ($name === 'trace') {
            return $this->trace
                ?? $this->trace = new ITrace($this->cloner->config->maxRedirectTimes);
        }
        return null;
    }

    public function __destruct()
    {
        // 清空数据
        unset(
            $this->driver,
            $this->cloner,
            $this->process,
            $this->longTermMemories,
            $this->sessionMemories,
            $this->trace,
            $this->contexts,
            $this->prev
        );

        SpyAgency::decr(static::class);
    }
}