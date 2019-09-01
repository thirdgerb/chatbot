<?php


namespace Commune\Chatbot\Config\Children;

use Commune\Chatbot\Framework\Providers;
use Commune\Chatbot\OOHost\HostConversationalServiceProvider;
use Commune\Chatbot\OOHost\HostProcessServiceProvider;
use Commune\Support\Option;

class BaseServicesConfig extends Option
{
    public static function stub(): array
    {
        return [
            'translation' => Providers\TranslatorServiceProvider::class,
            'render' => Providers\ReplyRendererServiceProvider::class,
            'logger' => Providers\LoggerServiceProvider::class,
            'event' => Providers\EventServiceProvider::class,
            'conversational' => Providers\ConversationalServiceProvider::class,
            'hostProcess' => HostProcessServiceProvider::class,
            'hostConversation' => HostConversationalServiceProvider::class,
        ];
    }


}