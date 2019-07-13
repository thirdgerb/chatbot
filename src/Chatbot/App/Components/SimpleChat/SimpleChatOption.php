<?php


namespace Commune\Chatbot\App\Components\SimpleChat;


use Commune\Support\Option;

/**
 * @property-read string $id
 * @property-read string $resource
 */
class SimpleChatOption extends Option
{
    public static function stub(): array
    {
        return [
            'id' => 'example',
            'resource' => __DIR__ . '/resources/example.yml',
        ];
    }


}