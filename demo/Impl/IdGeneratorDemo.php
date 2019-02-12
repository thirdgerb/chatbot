<?php

/**
 * Class IdGeneratorDemo
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Contracts\IdGenerator;
use Ramsey\Uuid\Uuid;

class IdGeneratorDemo implements IdGenerator
{
    public function makeChatId(string $userId, string $recipientId, string $platformId): string
    {
        return md5("uid:$userId:rid:$recipientId:pid:$platformId");
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function makeMessageUUId(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param string $chatId
     * @return string
     * @throws \Exception
     */
    public function makeSessionId(string $chatId): string
    {
        return Uuid::uuid4()->toString();
    }


}