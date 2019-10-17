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
     * @var int[]
     */
    protected $gcCounts = [];

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

        $this->gcCounts = $this->driver->getGcCounts($sessionId);
    }


    public function cacheSessionData(SessionData $data) : void
    {
        $type = $data->getSessionDataType();
        $id = $data->getSessionDataId();
        $this->cachedSessionData[$type][$id] = $data;

        if (
            $data instanceof Context
            // memory 不列入 gc
            && !($data instanceof Memory)
            && !isset($this->gcCounts[$id])
        ) {
            $this->gcCounts[$id] = 0;
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

    public function save(Session $session): void
    {
        // 当前上下文持有的 context id
        $holdingIds = [];

        // 遍历所有 snapshot, 并单独保存 breakpoint
        while ($snapshot = array_pop($this->snapshots)) {
            $snapshot->saved = true;
            $this->driver
                ->saveSnapshot(
                    $snapshot,
                    $session->hostConfig->sessionExpireSeconds
                );

            $holdingIds = array_merge($holdingIds, $snapshot->getContextIds());
        }

        // 这是被当前上下文所持有的 context.
        // todo yield 情况还需要专门考虑. 现在yield 是不完备的.
        $existsIds = [];
        foreach ($holdingIds as $id) {
            $existsIds[$id] = 1;
        }

        // gc 环节.
        $gcIds = [];
        foreach ($this->gcCounts as $id => $count) {
            if ($count === 0 && !array_key_exists($id, $existsIds)) {
                $gcIds[] = $id;
                // 剔除掉要 gc 的context
                unset($this->cachedSessionData[SessionData::CONTEXT_TYPE][$id]);
                // 完成了gc, 去掉计数器. 不然就内存泄露了.
                unset($this->gcCounts[$id]);
            }
        }

        // 执行 gc 的操作.
        if (!empty($gcIds)) {
            $this->driver->gcContexts($session, ...$gcIds);
            CHATBOT_DEBUG and $session->logger->debug(__METHOD__ . ' gc contexts of '. implode(',', $gcIds));
        }
        // 保存 gc 的计数.
        $this->driver->saveGcCounts($this->sessionId, $this->gcCounts);

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
        $this->cachedSessionData[SessionData::CONTEXT_TYPE] = [];

    }

    public function getGcCount(Context $context) : int
    {
        if ($context instanceof Memory) {
            return 0;
        }
        $contextId = $context->getId();
        return $this->gcCounts[$contextId] ?? $this->gcCounts[$contextId] = 0;
    }

    public function incrGcCount(Context $context): void
    {
        if ($context instanceof Memory) {
            return;
        }
        $contextId = $context->getId();
        $origin = intval($this->gcCounts[$contextId] ?? 0);
        $this->gcCounts[$contextId] = $origin + 1;
    }

    public function decrGcCount(Context $context): void
    {
        if ($context instanceof Memory) {
            return;
        }
        $contextId = $context->getId();
        $origin = intval($this->gcCounts[$contextId] ?? 0);
        if ($origin > 0) {
            $this->gcCounts[$contextId] = $origin - 1;
        }
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }

}