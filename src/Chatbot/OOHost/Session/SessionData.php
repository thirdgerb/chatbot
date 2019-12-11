<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Yielding;

/**
 * 可以在 Session 中存储的数据,
 */
interface SessionData
{
    const CONTEXT_TYPE = Context::class;
    const YIELDING_TYPE = Yielding::class;

    public function toSessionIdentity() : SessionDataIdentity;

    public function shouldSave(): bool;

    public function getSessionDataType() : string;

    public function getSessionDataId() : string;

}