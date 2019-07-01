<?php


namespace Commune\Chatbot\Config\Services;

use Commune\Chatbot\OOHost\OOHostServiceProvider;
use Commune\Support\Option;
use Commune\Chatbot\Framework\Providers;

/**
 * @property-read string $translation
 * @property-read string $hosting
 * @property-read string $logger
 * @property-read string $event
 * @property-read string $conversational
 *
 */
class BaseServiceConfig extends Option
{
    public static function stub(): array
    {
        return [
            'translation' => Providers\TranslatorServiceProvider::class,
            'hosting' => OOHostServiceProvider::class,
            'logger' => Providers\LoggerServiceProvider::class,
            'event' => Providers\EventServiceProvider::class,
            'conversational' => Providers\ConversationalServiceProvider::class,
        ];
    }


}