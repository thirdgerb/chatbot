<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;
use Commune\Blueprint\Ghost\MindMeta\EntityMeta;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Ghost\Component\AGhostComponent;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read bool $trigger     Predefined 功能的开关. 如果关闭的话, 所有功能都不会运行.
 *
 * 可以搞一个自定义组件继承本类, 再去实现其中自定义的功能.
 */
class PredefinedComponent extends AGhostComponent
{
    public static function stub(): array
    {
        return [
            'trigger' => true,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function bootstrap(App $app): void
    {
        if (!$this->trigger) {
            $app->getConsoleLogger()
                ->warning(
                    static::class
                    . ' not running, trigger is false'
                );
            return;
        }

        // 加载 emotion 配置.
        $this->loadResourceOption(
            $app,
            EmotionMeta::class,
            EmotionMeta::class,
            __DIR__ . '/resources/emotions.yml',
            false,
            FileStorageOption::OPTION_YML
        );

        // 加载 entity 配置.
        $this->loadResourceOption(
            $app,
            EntityMeta::class,
            EntityMeta::class,
            __DIR__ . '/resources/entities.yml',
            false,
            FileStorageOption::OPTION_YML
        );

        // 加载 intent 配置.
        $this->loadResourceOption(
            $app,
            IntentMeta::class,
            IntentMeta::class,
            __DIR__ . '/resources/intents.yml',
            false,
            FileStorageOption::OPTION_YML
        );

        // 加载代码
        $this->loadPsr4MindRegister(
            $app,
            [
                "Commune\\Components\\Predefined\\Memory" => __DIR__ . '/Memory',
                "Commune\\Components\\Predefined\\Intent" => __DIR__ . '/Intent',
                // "Commune\\Components\\Predefined\\Manager" => __DIR__ . '/Manager',
                "Commune\\Components\\Predefined\\Join" => __DIR__ . '/Join',
            ]
        );

        // 加载语言配置
        $this->loadTranslation(
            $app,
            __DIR__ . '/resources/trans',
            true,
            false
        );


    }


}