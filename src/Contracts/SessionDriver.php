<?php

/**
 * Class SessionDriver
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use Commune\Chatbot\Framework\Context\ContextData;
use Commune\Chatbot\Framework\Directing\History;

interface SessionDriver
{

    public function fetchContextDataById(string $id) : ? ContextData;

    public function saveContextData(ContextData $data);


    public function loadHistory(string $sessionId) : ? History;

    public function saveHistory(string $sessionId, History $history);

}