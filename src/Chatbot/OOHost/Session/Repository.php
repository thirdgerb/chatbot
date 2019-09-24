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

    public function getSnapshot(string $sessionId, string $belongsTo, bool $refresh = false) : Snapshot;

    public function clearSnapshot(string $sessionId, string $belongsTo) : void;

    public function getSnapshots() : array;


    public function flush(Session $session) : void;

    public function getDriver() : Driver;

}