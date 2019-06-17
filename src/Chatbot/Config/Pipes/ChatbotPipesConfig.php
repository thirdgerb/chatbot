<?php


namespace Commune\Chatbot\Config\Pipes;


use Commune\Chatbot\OOHost\OOHostPipe;
use Commune\Chatbot\App\ChatPipe\ChattingPipe;
use Commune\Chatbot\App\ChatPipe\MessengerPipe;
use Commune\Support\Option;

/**
 * @property-read string[] $onUserMessage
 */
class ChatbotPipesConfig extends Option
{
    public static function stub(): array
    {
        return [
            'onUserMessage' => [
                MessengerPipe::class,
                ChattingPipe::class,
                OOHostPipe::class
            ]
        ];
    }


}