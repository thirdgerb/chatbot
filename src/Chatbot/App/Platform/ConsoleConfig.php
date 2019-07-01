<?php


namespace Commune\Chatbot\App\Platform;


use Commune\Support\Option;

/**
 * @property-read string $chatbotUserId
 * @property-read string $consoleUserId
 * @property-read array $allowIPs  如果通过 tcp 来连接, 允许的用户的ip
 * @property-read string $consoleUserName
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
            'consoleUserName' => 'testUserName',
            'allowIPs' => [
                // 'IP'
            ],
            'ip' => '127.0.0.1',
            'port' => 9501,
        ];
    }


}