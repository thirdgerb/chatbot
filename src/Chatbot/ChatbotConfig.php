<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot;

use Commune\Chatbot\Prototype\Bootstrap\ConfigBindings;
use Commune\Framework\Prototype\ExpReporter\ExpReporterServiceProvider;
use Commune\Ghost\GhostConfig;
use Commune\Shell\ShellConfig;
use Commune\Support\Struct\Struct;
use Commune\Support\Struct\Structure;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $chatbotName           机器人的名称, 衡量机器人的唯一ID
 * @property-read bool $debug                   是否开启调试模式
 *
 * @property-read array $providers              需要注册的进程级服务.
 *
 * @property-read array $configs                全局绑定的配置, 都是 Struct 对象
 *  [
 *      'structClassName',          // 直接用类名
 *      'structClassName' => [[     // 会将数组作为初始化的数据
 *  ]
 *
 * @see Struct
 * @see ConfigBindings
 *
 * @property-read GhostConfig $ghost            Ghost 的定义
 * @property-read ShellConfig[] $shells         Shell 的定义
 */
class ChatbotConfig extends Structure
{
    const IDENTITY = 'chatbotName';

    protected static $associations =[
        'ghost' => GhostConfig::class,
        'shells[]' => ShellConfig::class,
    ];

    public static function stub(): array
    {
        return [
            'chatbotName' => 'commune-demo',
            'debug' => true,
            'providers' => [

                ExpReporterServiceProvider::class,

            ],
            'configs' => [],
            'ghost' => GhostConfig::stub(),
            'shells' => [
                ShellConfig::stub(),
            ]
        ];
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['chatbotName'])) {
            return 'chatbotName is not defined';
        }

        return null;
    }

}