<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Ghost\MindMeta\EntityMeta;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;
use Commune\Components\Tree\TreeComponent;
use Commune\Ghost\Component\AGhostComponent;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoComponent extends AGhostComponent
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
        // 注册组件.
        $this->loadPsr4MindRegister(
            $app,
            [
                'Commune\Components\Demo\Contexts' => __DIR__ . '/Contexts',
                'Commune\Components\Demo\Recall' => __DIR__ . '/Recall',
                'Commune\Components\Demo\Maze' => __DIR__ . '/Maze',
                'Commune\Components\Demo\Git' => __DIR__ . '/Git',
            ]
        );

        $mindset = [
            'entities' => EntityMeta::class,
            'intents' => IntentMeta::class,
            'synonyms' => SynonymMeta::class,
        ];

        foreach ($mindset as $file => $optionClass) {
            $this->loadResourceOption(
                $app,
                $optionClass,
                $optionClass,
                __DIR__ . '/resources/mind/' . $file . '.yml',
                false,
                FileStorageOption::OPTION_YML
            );
        }


        $this->loadTranslation(
            $app,
            __DIR__ . '/resources/trans',
            true,
            false
        );


        $this->dependComponent($app, TreeComponent::class);
    }

}