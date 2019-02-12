<?php

/**
 * Class IdGenerator
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


interface IdGenerator
{

    public function makeChatId(string $userId, string $recipientId, string $platformId): string;

    public function makeMessageUUId() : string;

    public function makeSessionId(string $chatId) : string;

}