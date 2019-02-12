<?php

/**
 * Class SessionDriverDemo
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Context\ContextData;
use Commune\Chatbot\Framework\Directing\History;

class SessionDriverDemo implements SessionDriver
{
    protected $contexts = [];

    protected $histories = [];

    public function fetchContextDataById(string $id): ? ContextData
    {
        return $this->contexts[$id] ?? null;
    }

    public function saveContextData(ContextData $data)
    {
        $this->contexts[$data->getId()] = $data;
    }

    public function loadHistory(string $sessionId): ? History
    {
        return $this->histories[$sessionId] ?? null;
    }

    public function saveHistory(string $sessionId, History $history)
    {
        $this->histories[$sessionId] = $history;
    }


}