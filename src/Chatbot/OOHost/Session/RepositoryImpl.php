<?php

/**
 * Class RepositoryImpl
 * @package Commune\Chatbot\OOHost\Session
 */

namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

class RepositoryImpl implements Repository, RunningSpy, HasIdGenerator
{
    use RunningSpyTrait, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Driver
     */
    public $driver;

    /*----------- cached -----------*/

    /**
     * @var SessionData[][]
     */
    protected $cachedSessionData = [];


    /**
     * @var Snapshot[]
     */
    protected $snapshots = [];

    /**
     * contextId 相互持有的关系.
     *  $contextId => $count
     * @var array [int, name]
     */
    protected $gcCounts = [];

    /**
     * 所有 snapshot 持有的 contextId
     *  $snapshot->belongsTo => [
     *      $contextId,
     *  ]
     *
     * @var string[][]
     */
    protected $snapshotContextIds = [];

    /**
     * RepositoryImpl constructor.
     * @param string $traceId
     * @param string $sessionId
     * @param Driver $driver
     */
    public function __construct(
        string $traceId,
        string $sessionId,
        Driver $driver
    )
    {
        $this->driver = $driver;
        $this->sessionId = $sessionId;
        $this->traceId = $traceId;
        static::addRunningTrace($this->traceId, $this->traceId);
    }


    public function cacheSessionData(SessionData $data) : void
    {
        $type = $data->getSessionDataType();
        $id = $data->getSessionDataId();
        $this->cachedSessionData[$type][$id] = $data;

        if ( $data instanceof Context ) {
            $this->getGcCount($data);
        }
    }

    public function fetchSessionData(
        Session $session,
        SessionDataIdentity $id,
        \Closure $makeDefault = null
    ) : ? SessionData
    {
        if (isset($this->cachedSessionData[$id->type][$id->id])) {
            $data =  $this->cachedSessionData[$id->type][$id->id];
            return $data;
        }

        switch($id->type) {
            case SessionData::CONTEXT_TYPE :
                $data = $this->driver->findContext($session, $id->id);
                break;
            case SessionData::YIELDING_TYPE :
                $data = $this->driver->findYielding($id->id);
                break;
            default :
                $data = null;
        }

        // 如果都没保存, 则用传入的闭包生成一个新的实例, 并保存.
        if (!isset($data) && isset($makeDefault)) {
            $data = $makeDefault();
        }

        if ($data instanceof SessionData) {
            $this->cacheSessionData($data);
        }

        if ($data instanceof SessionInstance && !$data->isInstanced()) {
            return $data->toInstance($session);
        }

        return $data;

    }

    public function getSnapshots(): array
    {
        return $this->snapshots;
    }


    public function getSnapshot(string $sessionId, string $belongsTo, bool $refresh = false) : Snapshot
    {
        if (isset($this->snapshots[$belongsTo])) {
            return $this->snapshots[$belongsTo];
        }

        if ($refresh) {
            return $this->snapshots[$belongsTo] = new Snapshot($sessionId, $belongsTo);
        }

        $cached = $this->getDriver()->findSnapshot($sessionId, $belongsTo);
        if (!empty($cached)) {
            // 如果 snapshot 的 saved 为false,
            // 说明出现重大错误, 导致上一轮没有saved.
            // 这时必须从头开始, 否则永远卡在错误这里.
            if ($cached instanceof Snapshot && $cached->saved) {
                $cached->saved = false;
                return $this->snapshots[$belongsTo] = $cached;
            }
        }

        // 创建一个新的snapshot
        return $this->snapshots[$belongsTo] = new Snapshot($sessionId, $belongsTo);
    }

    public function clearSnapshot(string $sessionId, string $belongsTo): void
    {
        // 这样就不会保存了. 但是 snapshot 并不会立刻从别的history里清除掉.
        unset($this->snapshots[$belongsTo]);
        $this->driver->clearSnapshot($sessionId, $belongsTo); // 系统也不保存了.
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }


    /*--------------- save 环节 ----------------*/

    public function save(Session $session): void
    {
        $this->loadGcCounts($session->sessionId);
        $this->syncSnapshots($session);
        $gcIds = $this->fetchGCIds();
        $this->runGcContextIds($session, $gcIds);
        $this->saveGcCounts($this->sessionId);
        $this->saveCachedContexts($session);
    }

    protected function syncSnapshots(Session $session) : void
    {
        // 遍历所有 snapshot, 并单独保存 breakpoint
        while ($snapshot = array_pop($this->snapshots)) {
            $snapshot->saved = true;
            $this->driver
                ->saveSnapshot(
                    $snapshot,
                    $session->hostConfig->sessionExpireSeconds
                );
            // 存在的才改进.
            $this->snapshotContextIds[$snapshot->belongsTo] = $snapshot->getContextIds();
        }
    }


    protected function fetchGCIds() : array
    {
        $existsIds = [];
        foreach ($this->snapshotContextIds as $belongsTo => $contextIds) {
            foreach ($contextIds as $id) {
                $existsIds[$id] = 1;
            }
        }

        // gc 环节.
        $gcIds = [];
        foreach ($this->gcCounts as $id => list($count, $name)) {
            if ($count === 0 && !array_key_exists($id, $existsIds)) {
                $gcIds[$id] = $name;
                // 剔除掉要 gc 的context
                unset($this->cachedSessionData[SessionData::CONTEXT_TYPE][$id]);
                // 完成了gc, 去掉计数器. 不然就内存泄露了.
                unset($this->gcCounts[$id]);
            }
        }
        return $gcIds;
    }

    protected function saveCachedContexts(Session $session) : void
    {
        // 执行 context 数据保存.
        $cachedContexts = $this->cachedSessionData[SessionData::CONTEXT_TYPE] ?? [];
        if (empty($cachedContexts)) {
            return ;
        }

        foreach ($cachedContexts as $id => $data) {
            // 数据有改动的话, 则需要保存.
            if ($data->shouldSave()) {
                $this->driver->saveContext($session, $data);
            }
        }

        // 清空当前缓存的所有数据.
        $this->cachedSessionData[SessionData::CONTEXT_TYPE] = [];
    }


    /*--------------- gc 环节 ----------------*/

    public function getGcCount(Context $context) : int
    {
        // memory 不做 gc
        if ($context instanceof Memory) {
            return 1;
        }

        $id = $context->getId();
        if (isset($this->gcCounts[$id])) {
            return $this->gcCounts[$id][0];
        }
        $this->gcCounts[$id] = [0, $context->getName()];
        return 0;
    }

    public function incrGcCount(Context $context): void
    {
        // memory 不做GC
        if ($context instanceof Memory) {
            return;
        }

        $count = $this->getGcCount($context) + 1;
        $contextId = $context->getId();
        $this->gcCounts[$contextId][0] = $count;
    }

    public function decrGcCount(Context $context): void
    {
        // memory 不做GC
        if ($context instanceof Memory) {
            return;
        }

        $contextId = $context->getId();
        $count = $this->getGcCount($context);
        if ($count > 0) {
            $this->gcCounts[$contextId][0] = $count - 1;
        }
    }

    protected function runGcContextIds(Session $session, array $gcIds) : void
    {
        // 执行 gc 的操作.
        if (empty($gcIds)) {
            return;
        }

        $ids = array_keys($gcIds);
        $this->driver->gcContexts($session, ...$ids);

        if (! CHATBOT_DEBUG) {
            return;
        }

        $info = '';
        foreach ($gcIds as $id => $name) {
            $info .= "$id:$name, ";
        }

        $session->logger->debug(
            __METHOD__
            . ' gc contexts of '
            . $info
        );
    }

    protected function saveGcCounts(string $sessionId) : void
    {
        // 保存 gc 的计数.
        $this->driver
            ->saveGcCounts(
                $sessionId,
                [
                    'gcCounts' => $this->gcCounts,
                    'snapshotContextIds' => $this->snapshotContextIds,
                ]
            );
    }

    protected function loadGcCounts(string $sessionId) : void
    {
        $gcCounts = $this->driver->getGcCounts($sessionId);
        $this->gcCounts = $gcCounts['gcCounts'] ?? [];
        $this->snapshotContextIds = $gcCounts['snapshotContextIds'] ?? [];
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }

}