<?php

namespace Commune\Chatbot\Framework\Constants;

class CacheKey
{
    const SESSION_ID_KEY = 'sessionId:%s';

    const CHAT_LOCKER = 'chatLocker:%s';

    const MEMORY_LOCKER = 'memoryLock:%s';

    public static function toSessionIdKey(string $belongsTo) : string
    {
        return sprintf(self::SESSION_ID_KEY, $belongsTo);
    }
}