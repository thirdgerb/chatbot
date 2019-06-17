<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Breakpoint;

interface SessionData
{

    const BREAK_POINT = Breakpoint::class;
    const CONTEXT_TYPE = Context::class;

    public function toSessionIdentity() : SessionDataIdentity;

    public function shouldSave(): bool;

    public function getSessionDataType() : string;

    public function getSessionDataId() : string;

}