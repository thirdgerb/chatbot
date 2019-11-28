<?php


namespace Commune\Chatbot\Config\Children;


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
                // 管理所有的异常, 发送消息
                MessengerPipe::class,
                // 阻塞会话 chat, 避免同时接受多个消息产生歧义
                ChattingPipe::class,
                // 多轮对话管理
                OOHostPipe::class
            ]
        ];
    }


}