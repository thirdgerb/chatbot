<?php

/**
 * Class ChatbotConfig
 * @package Commune\Chatbot\Config
 */

namespace Commune\Chatbot\Config;

use Commune\Chatbot\Config\Logger\LoggerConfig;
use Commune\Chatbot\Config\Message\DefaultMessagesConfig;
use Commune\Chatbot\Config\Pipes\ChatbotPipesConfig;
use Commune\Chatbot\Config\Translation\TranslationConfig;
use Commune\Chatbot\Config\Event\EventListenerConfig;
use Commune\Chatbot\Config\Host\OOHostConfig;
use Commune\Support\Option;

/**
 * Interface ChatbotConfig
 * @package Commune\Chatbot\Config
 *
 * @property-read bool $debug
 * @property-read array $configBindings
 *
 * @property-read string[] $reactorProviders
 * @property-read string[] $conversationProviders
 * @property-read string[] $components
 *
 * @property-read TranslationConfig $translation
 * @property-read LoggerConfig $logger
 *
 * @property-read ChatbotPipesConfig $chatbotPipes
 *
 * @property-read EventListenerConfig[] $eventRegister
 *
 * @property-read DefaultMessagesConfig $defaultMessages
 *
 * @property-read OOHostConfig $host
 */
class ChatbotConfig extends Option
{
    protected static $associations = [
        'defaultMessages' => DefaultMessagesConfig::class,
        'eventRegister[]' => EventListenerConfig::class,
        'chatbotPipes' => ChatbotPipesConfig::class,
        'translation' => TranslationConfig::class,
        'logger' => LoggerConfig::class,
        'host' => OOHostConfig::class,
    ];

    public static function stub(): array
    {
        return [
            'debug' => true,

            'configBindings' => [],

            'components' => [
                // 'componentName',
                // 'componentName' => []
            ],

            // 用户自定义的组件.
            'reactorProviders' => [
            ],

            // 用户自定义的组件.
            'conversationProviders' => [
            ],

            'chatbotPipes' => ChatbotPipesConfig::stub(),

            'translation' => TranslationConfig::stub(),
            'logger' => LoggerConfig::stub(),

            'defaultMessages' => DefaultMessagesConfig::stub(),
            'eventRegister' => [
                EventListenerConfig::stub(),
            ],

            'host' => OOHostConfig::stub(),
        ];
    }

}