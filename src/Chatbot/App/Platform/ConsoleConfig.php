<?php


namespace Commune\Chatbot\App\Platform;


use Commune\Support\Option;

/**
 * @property-read string $chatbotUserId
 * @property-read string $consoleUserId
 * @property-read string $userName
 * @property-read string $ip
 * @property-read int $port
 */
class ConsoleConfig extends Option
{
    public static function stub(): array
    {
        return [
            'chatbotUserId' => 'testChatbotUserId',
            'consoleUserId' => 'testUserId',
            'userName' => 'testUserName',
            'ip' => '127.0.0.1',
            'port' => 9501,
        ];
    }


}