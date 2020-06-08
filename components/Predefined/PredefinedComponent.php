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
use Commune\Ghost\Component\GhostComponent;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class PredefinedComponent extends GhostComponent
{
    public static function stub(): array
    {
        return [];
    }

    public static function relations(): array
    {
        return [];
    }

    public function bootstrap(App $app): void
    {
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
            ]
        );
    }


}