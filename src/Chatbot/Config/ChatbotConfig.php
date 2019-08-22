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
use Commune\Chatbot\Framework\Providers;
use Commune\Chatbot\OOHost\HostConversationalServiceProvider;
use Commune\Chatbot\OOHost\HostProcessServiceProvider;
use Commune\Support\Option;

/**
 * Interface ChatbotConfig
 * @package Commune\Chatbot\Config
 *
 * @property-read string $chatbotName name of current chatbot
 *
 * @property-read bool $debug
 *
 * @property-read array $configBindings preload config. immutable in process
 *
 * @property-read array $baseServices chatbot system service binding. could modify
 * @property-read string[] $processProviders process level service providers
 * @property-read string[] $conversationProviders conversation(request) level service providers
 * @property-read string[] $components register chatbot components
 *
 * @property-read TranslationConfig $translation  translator configs
 * @property-read LoggerConfig $logger 系统的日志配置.
 *
 * @property-read ChatbotPipesConfig $chatbotPipes
 *
 * @property-read EventListenerConfig[] $eventRegister
 *
 * @property-read DefaultMessagesConfig $defaultMessages
 *
 * @property-read OOHostConfig $host  multi-turn conversation kernel config
 *
 * @property-read array $slots environment slots. multidimensional array will flatten to key-value array ([a][b][c] to a.b.c)
 */
class ChatbotConfig extends Option
{
    const IDENTITY = 'chatbotName';


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
            'chatbotName' => 'chatbotName',

            'debug' => true,

            // 预定义的 slots
            'slots' => [],

            'configBindings' => [],

            'components' => [
                // 'componentName',
                // 'componentName' => []
            ],

            // 系统预注册的服务.
            'baseServices' => [
                'translation' => Providers\TranslatorServiceProvider::class,
                'logger' => Providers\LoggerServiceProvider::class,
                'event' => Providers\EventServiceProvider::class,
                'conversational' => Providers\ConversationalServiceProvider::class,
                'hostProcess' => HostProcessServiceProvider::class,
                'hostConversation' => HostConversationalServiceProvider::class,
            ],

            // 用户自定义的进程级组件.
            'processProviders' => [
            ],

            // 用户自定义的请求级组件.
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