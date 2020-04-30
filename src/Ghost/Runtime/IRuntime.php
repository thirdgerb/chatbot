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

use Commune\Blueprint\Exceptions\IO\SaveDataFailException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Ghost\Runtime\Thread;
use Commune\Contracts\Ghost\RuntimeDriver;
use Commune\Ghost\Memory\IRecollection;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


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
     * @var RuntimeDriver|null
     */
    protected $driver;

    /**
     * @var string
     */
    protected $traceId;

    /*---- cached ----*/

    /**
     * @var string
     */
    protected $currentProcessId = '';

    /**
     * @var Process|null[]
     */
    protected $processes = [];

    /**
     * @var Recollection[]
     *
     *  [string $id => Recollection $recollection, ]
     */
    protected $recollections = [];

    /**
     * @var Thread[]
     */
    protected $yielding = [];

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

        static::addRunningTrace($this->traceId, $this->traceId);
    }


    /*---- processes ----*/

    public function getCurrentProcess(): Process
    {
        // 已经缓存过.
        if (!empty($this->currentProcessId)) {
            return $this->processes[$this->currentProcessId];
        }

        // 无状态下都是新生成.
        if (!$this->cloner->isStateless()) {
            $pId = $cloner->storage[ClonerStorage::CURRENT_PROCESS_ID] ?? '';
            $process = $this->findProcess($pId);
            if (isset($process)) {
                // 生成一个新的 Snapshot
                $process = $process->nextSnapshot($this->cloner->getTraceId());

                // 新的 Id
                $this->currentProcessId = $process->id;
                $this->processes[$pId] = $process;

                return $process;
            }
        }

        // 创建一个新的.
        $contextName = $this->cloner->scene->contextName;
        $process = $this->createProcess($contextName);
        $this->currentProcessId = $process->id;
        return $process;
    }

    public function findRecollection(string $id): ? Recollection
    {
        if (isset($this->recollections[$id])) {
            return $this->recollections[$id];
        }

        if (!isset($this->driver)) {
            return null;
        }

        $cacheExists = $this->driver->cacheExists();

        if (!$cacheExists) {
            return null;
        }

        $obj = $this->driver->fetchCachable($id) ?? $this->driver->fetchSavable($id);
        if ($obj instanceof Recollection) {
            $this->recollections[$obj->getId()] = $obj;
            return $this->recollections[$id] = $obj;
        }
        return null;
    }

    public function createRecollection(
        string $id,
        string $name,
        bool $longTerm,
        array $defaults
    ): Recollection
    {
        $recollection = new IRecollection(
            $id,
            $name,
            $longTerm,
            $defaults
        );
        $this->recollections[$recollection->getId()] = $recollection;
        return $recollection;
    }

    public function addRecollection(Recollection $recollection): void
    {
        $this->recollections[$recollection->getId()] = $recollection;
    }

    public function toContextMsg(): ? IContextMsg
    {
        $process = $this->getCurrentProcess();
        $node = $process->changedNode();

        if (!isset($node)) {
           return null;
        }
        $context = $node->findContext($this->cloner);
        return new IContextMsg([
            'contextName' => $node->contextName,
            'contextId' => $node->contextId,
            'data' => $context->toArray()
        ]);
    }





    public function setCurrentProcess(Process $process): void
    {
        $id = $process->id;
        $this->processes[$id] = $process;
        $this->currentProcessId = $id;
    }

    public function createProcess(string $contextName): Process
    {
        $context = $this->cloner->newContext($contextName);
        $node = $context->toNewNode();

        $process = new IProcess(
            $this->cloner->getClonerId(),
            $node,
            null// self create new processId
        );

        $this->processes[$process->id] = $process;
        return $process;
    }

    public function findProcess(string $processId): ? Process
    {
        http_build_query();
        if (array_key_exists($processId, $this->processes)) {
            return $this->processes[$processId];
        }

        if (!isset($this->driver)) {
            return null;
        }

        $process = $this->driver->fetchCachable($processId);
        if (!$process instanceof Process) {
            $process = null;
        }

        return $this->processes[$processId] = $process;
    }

    public function save(): void
    {
        if ($this->cloner->isStateless()) {
            return;
        }

        if (!isset($this->driver)) {
            return;
        }

        $storage = $this->cloner->storage;
        $storage[ClonerStorage::CURRENT_PROCESS_ID] = $this->currentProcessId;

        $cachable = [];
        $recollectionIds = [];
        $savable = [];

        // 进程都存缓存.
        foreach ($this->processes as $process) {
            if ($process instanceof Process && $process->isCaching()) {
                $cachable[$process->getCachableId()] = $process;
            }
        }

        // yielding 目前也放在 cachable 里保存. session 相关.
        foreach ($this->yielding as $thread) {
            if ($thread->isCaching()) {
                $cachable[$thread->getCachableId()] = $thread;
            }
        }

        // 记忆看情况, 都缓存, 部分存数据库.
        foreach ($this->recollections as $recollection) {
            if ($recollection->isCaching()) {
                $recollectionId = $recollection->getCachableId();
                $recollectionIds[] = $recollectionId;

                $cachable[$recollectionId] = $recollection;
            }

            if ($recollection->isSaving()) {
                $savable[$recollection->getSavableId()] = $recollection;
            }
        }

        // 缓存上一轮对话改变过的数据. 假设改变过的数据更有可能会改变.
        $storage = $this->cloner->storage;
        $storage[ClonerStorage::LAST_RECOLLECTION_IDS] = $recollectionIds;

        // 异常.
        $e = null;
        $success = false;
        try {
            $expire = $this->cloner->getSessionExpire();
            $success = $this->driver->cacheCachable($cachable, $expire) && $this->driver->saveSavable($savable);
        } catch (\Throwable $e) {
        }

        if (!$success || isset($e)) {
            throw new SaveDataFailException(
                __METHOD__,
                $this->traceId
            );
        }
    }


    public function __destruct()
    {
        // 清空数组.
        $this->processes = [];
        $this->yielding = [];
        $this->recollections = [];

        static::removeRunningTrace($this->traceId);
    }
}