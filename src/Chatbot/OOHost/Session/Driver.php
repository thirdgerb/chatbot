<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Chatbot\OOHost\History\Yielding;

interface Driver extends RunningSpy
{

    /*------- snapshot -------*/

    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0) : void;

    public function findSnapshot(string $sessionId, string $belongsTo) : ? Snapshot;

    public function clearSnapshot(string $sessionId, string $belongsTo) : void;

    /*------- yielding -------*/

    public function saveYielding(Session $session, Yielding $yielding) : void;

    public function findYielding(string $contextId) : ? Yielding;

    /*------- breakpoint -------*/

    public function saveBreakpoint(Session $session, Breakpoint $breakpoint) : void;

    public function findBreakpoint(Session $session, string $id) : ? Breakpoint;

    /*------- context -------*/

    public function saveContext(Session $session, Context $context) : void;

    public function findContext(Session $session, string $contextId) : ? Context;

}