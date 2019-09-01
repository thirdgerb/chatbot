<?php

/**
 * Class ChatbotConfig
 * @package Commune\Chatbot\Config
 */

namespace Commune\Chatbot\Config;

use Commune\Chatbot\Config\Children\BaseServicesConfig;
use Commune\Chatbot\Config\Children\LoggerConfig;
use Commune\Chatbot\Config\Children\DefaultMessagesConfig;
use Commune\Chatbot\Config\Children\ChatbotPipesConfig;
use Commune\Chatbot\Config\Children\TranslationConfig;
use Commune\Chatbot\Config\Children\OOHostConfig;

use Commune\Support\Option;

/**
 * Interface ChatbotConfig
 * @package Commune\Chatbot\Config
 *
 *
 * @property-read string $chatbotName  机器人的名字|name of current chatbot
 *
 * @property-read bool $debug 是否开启debug
 *
 * @property-read array $configBindings  注册预加载的配置. 进程中不变|preload config. immutable in the process
 *
 * @property-read BaseServicesConfig $baseServices 系统默认注册的服务. 可按需更改|chatbot system service binding. could modify
 *
 * @property-read string[] $processProviders 进程级服务注册|process level service providers
 *
 * @property-read string[] $conversationProviders 请求级别服务注册|conversation(request) level service providers
 *
 * @property-read string[] $components 注册Chatbot的Component|register chatbot components
 *
 * @property-read TranslationConfig $translation  i18n模块的配置|translator configs
 *
 * @property-read LoggerConfig $logger 系统的日志配置|default logger service
 *
 * @property-read ChatbotPipesConfig $chatbotPipes 请求流经的管道|pipeline to handle incoming message
 *
 * @property-read DefaultMessagesConfig $defaultMessages 默认的回复消息|default message for default event such as tooBusy
 *
 * @property-read OOHostConfig $host  多轮对话的核心模块配置|multi-turn conversation kernel config
 *
 */
class ChatbotConfig extends Option
{
    const IDENTITY = 'chatbotName';


    protected static $associations = [
        'defaultMessages' => DefaultMessagesConfig::class,
        'chatbotPipes' => ChatbotPipesConfig::class,
        'translation' => TranslationConfig::class,
        'logger' => LoggerConfig::class,
        'host' => OOHostConfig::class,
        'baseServices' => BaseServicesConfig::class,
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
            'baseServices' => BaseServicesConfig::stub(),

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

            'host' => OOHostConfig::stub(),
        ];
    }

}