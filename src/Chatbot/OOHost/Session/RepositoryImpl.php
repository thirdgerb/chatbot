<?php

/**
 * Class RepositoryImpl
 * @package Commune\Chatbot\OOHost\Session
 */

namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
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
     * RepositoryImpl constructor.
     * @param string $traceId
     * @param Driver $driver
     */
    public function __construct(
        string $traceId,
        Driver $driver
    )
    {
        $this->driver = $driver;
        $this->traceId = $traceId;
        static::addRunningTrace($this->traceId, $this->traceId);
    }


    public function cacheSessionData(SessionData $data) : void
    {
        $type = $data->getSessionDataType();
        $id = $data->getSessionDataId();
        $this->cachedSessionData[$type][$id] = $data;
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
            case SessionData::BREAK_POINT :
                $data = $this->driver->findBreakpoint($session, $id->id);
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


    public function getSnapshot(string $sessionId, string $belongsTo) : Snapshot
    {
        if (isset($this->snapshots[$belongsTo])) {
            return $this->snapshots[$belongsTo];
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

    public function flush(Session $session): void
    {
        while ($snapshot = array_pop($this->snapshots)) {
            $snapshot->saved = true;
            $this->driver
                ->saveSnapshot(
                    $snapshot,
                    $session->hostConfig->sessionExpireSeconds
                );

            $breakpoint = $snapshot->breakpoint;
            if (isset($breakpoint)) {
                $this->driver->saveBreakpoint($session, $breakpoint);
            }
        }

        $cachedContexts = $this->cachedSessionData[SessionData::CONTEXT_TYPE] ?? [];
        if (empty($cachedContexts)) {
            return ;
        }

        foreach ($cachedContexts as $id => $data) {
            if ($data->shouldSave()) {
                $this->driver->saveContext($session, $data);
            }
        }
        $this->cachedSessionData[SessionData::CONTEXT_TYPE] = [];
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }

}