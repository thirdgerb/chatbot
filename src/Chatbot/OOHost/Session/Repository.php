<?php

namespace Commune\Chatbot\OOHost\Session;
use Commune\Chatbot\OOHost\Context\Context;

/**
 * Session 数据的读写.
 */
interface Repository
{
    /**
     * 缓存 session 的数据.
     * @param SessionData $data
     */
    public function cacheSessionData(SessionData $data) : void;

    /**
     * 获取 session data
     * @param Session $session
     * @param SessionDataIdentity $id
     * @param \Closure|null $makeDefault
     * @return SessionData|null
     */
    public function fetchSessionData(
        Session $session,
        SessionDataIdentity $id,
        \Closure $makeDefault = null
    ) : ? SessionData;

    /**
     * 获取或创建一个 snapshot
     * @param string $sessionId
     * @param string $belongsTo
     * @param bool $refresh 是否创建一个新的snapshot
     * @return Snapshot
     */
    public function getSnapshot(string $sessionId, string $belongsTo, bool $refresh = false) : Snapshot;

    /**
     * 清除一个snapshot
     * @param string $sessionId
     * @param string $belongsTo
     */
    public function clearSnapshot(string $sessionId, string $belongsTo) : void;

    /**
     * 获取当前 session 所有 subDialog 的snapshot
     * @return array
     */
    public function getSnapshots() : array;

    /**
     * 保存当前 session 的数据.
     * @param Session $session
     */
    public function save(Session $session) : void;

    /**
     * @return Driver
     */
    public function getDriver() : Driver;


    /*-------- gc --------*/

    public function incrGcCount(Context $context) : void;

    public function decrGcCount(Context $context) : void;

    public function getGcCount(Context $context) : int;


}