<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Resource;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;
use Commune\Blueprint\Ghost\MindMeta\EntityMeta;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;
use Commune\Framework\Component\AComponentOption;
use Commune\Support\Utils\StringUtils;


/**
 * 加载系统 resource 路径下自定义配置的组件.
 * 会将所有 resource 路径下定义的 Option 加载到 OptRegistry 中.
 *
 *
 * 将 runtime 和 resources 拆分, 就不怕 CommuneEnv::isResetRegistry 破坏性过大了.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read bool $force                   是否强制加载
 * @property-read string $rootPath              根目录.
 * @property-read ResourceOption[] $predefined  资源的定义.
 * @property-read ResourceOption[] $resources   自定义加载的组件.
 * @property-read string $langPath              语言文件的路径名.
 */
class ResourceComponent extends AComponentOption
{
    public static function stub(): array
    {
        return [
            'force' => CommuneEnv::isResetRegistry(),
            'rootPath' => CommuneEnv::getResourcePath(),
            'langPath' => 'lang',
            'resources' => [],
            'predefined' => self::predefined(),
        ];
    }

    public static function predefined(array ...$appends) : array
    {
        $predefined = [
            [
                'name' => 'contexts',
                'optionClass' => ContextMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_PHP,
            ],
            [
                'name' => 'stages',
                'optionClass' => StageMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_PHP,
            ],
            [
                'name' => 'intents',
                'optionClass' => IntentMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_PHP,
            ],
            [
                'name' => 'emotions',
                'optionClass' => EmotionMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_PHP,
            ],
            [
                'name' => 'entities',
                'optionClass' => EntityMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_YML,
            ],
            [
                'name' => 'synonyms',
                'optionClass' => SynonymMeta::class,
                'isDir' => true,
                'loader' => ResourceOption::LOADER_YML,
            ],
        ];

        array_push($predefined, ...$appends);
        return $predefined;
    }

    public static function relations(): array
    {
        return [
            'resources[]' => ResourceOption::class,
            'predefined[]' => ResourceOption::class,
        ];
    }

    public function bootstrap(App $app): void
    {
        $rootPath = $this->rootPath;
        $this->loadTranslation(
            $app,
            StringUtils::gluePath(
                $rootPath,
                $this->langPath
            ),
            true,
            $this->force
        );

        $resources = array_merge($this->predefined, $this->resources);
        $force = $this->force;
        foreach ($resources as $resource) {
            /**
             * @var ResourceOption $resource
             */
            $cateName = $resource->category;
            $cateName = empty($cateName) ? $resource->optionClass : $cateName;
            $isDir = $resource->isDir;
            $resourcePath = $isDir
                ? StringUtils::gluePath(
                        $rootPath,
                        $resource->name
                    )
                : StringUtils::gluePath(
                        $rootPath,
                        $resource->name . '.' . $resource->loader
                    );

            $this->loadResourceOption(
                $app,
                $cateName,
                $resource->optionClass,
                $resourcePath,
                $isDir,
                $resource->loader,
                $force
            );
        }
    }


}