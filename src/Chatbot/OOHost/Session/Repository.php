<?php


namespace Commune\Chatbot\OOHost\Session;

interface Repository
{

    public function cacheSessionData(SessionData $data) : void;

    public function fetchSessionData(
        Session $session,
        SessionDataIdentity $id,
        \Closure $makeDefault = null
    ) : ? SessionData;

    public function getSnapshot(string $belongsTo, string $sessionId = null) : Snapshot;

    public function getSnapshots() : array;

    public function clearSnapshot(string $belongsTo) : void;

    public function flush(Session $session) : void;

    public function getDriver() : Driver;

}