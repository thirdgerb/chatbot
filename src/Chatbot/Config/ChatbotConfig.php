<?php

/**
 * Class ChatbotConfig
 * @package Commune\Chatbot\Config
 */

namespace Commune\Chatbot\Config;

use Commune\Chatbot\Config\Logger\LoggerConfig;
use Commune\Chatbot\Config\Message\DefaultMessagesConfig;
use Commune\Chatbot\Config\Pipes\ChatbotPipesConfig;
use Commune\Chatbot\Config\Services\BaseServiceConfig;
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
 * @property-read BaseServiceConfig $baseServices
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
 *
 * @property-read array $slots 环境变量. 会flatten ([a][b][c] 变成 a.b.c) 然后放到 slots 里面.
 */
class ChatbotConfig extends Option
{
    protected static $associations = [
        'defaultMessages' => DefaultMessagesConfig::class,
        'eventRegister[]' => EventListenerConfig::class,
        'chatbotPipes' => ChatbotPipesConfig::class,
        'translation' => TranslationConfig::class,
        'baseServices' => BaseServiceConfig::class,
        'logger' => LoggerConfig::class,
        'host' => OOHostConfig::class,
    ];

    public static function stub(): array
    {
        return [
            'debug' => true,

            // 预定义的 slots
            'slots' => [],

            'configBindings' => [],

            'components' => [
                // 'componentName',
                // 'componentName' => []
            ],

            // 系统的服务.
            'baseServices' => BaseServiceConfig::stub(),

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