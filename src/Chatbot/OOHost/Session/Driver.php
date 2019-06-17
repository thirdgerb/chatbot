<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Chatbot\OOHost\History\Yielding;

interface Driver
{

    public function saveYielding(Session $session, Yielding $yielding) : void;

    public function findYielding(string $contextId) : ? Yielding;


    public function saveBreakpoint(Session $session, Breakpoint $breakpoint) : void;
    public function findBreakpoint(Session $session, string $id) : ? Breakpoint;


    public function saveContext(Session $session, Context $context) : void;

    public function findContext(Session $session, string $contextId) : ? Context;

}