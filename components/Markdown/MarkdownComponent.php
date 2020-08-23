<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\App;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Components\Markdown\Providers\MDGroupContextLoader;
use Commune\Components\Markdown\Providers\MDOptRegistryProvider;
use Commune\Components\Tree\TreeComponent;
use Commune\Framework\Component\AComponentOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Utils\StringUtils;


/**
 * 核心组件! 用 markdown 文档来撰写多轮对话
 * 这个组件基本完成, chatlog 项目就初步齐活了.
 *
 * 目标是可以自动解析 markdown 文档, 得到树状结构的数据.
 * 然后把树状结构的数据使用指定的 Parser 解析为 TreeContext
 *
 * 在 TreeContext 里使用指定的 markdownStrategy 管理上下文逻辑.
 *
 * 用 @comment 注解的形式来定义其中的对话逻辑细节. 包括视频对话.
 * php 8.0 会实装 Attribute 功能, 现阶段先称注解的做法为 Annotation.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read bool $reset               是否重置配置. 否则只更新 updated
 * @property-read string $langDir           相关文本的配置路径.
 * @property-read MDGroupOption[] $groups
 *
 * # 可选配置
 *
 * @property-read string $resourcePath      放置 markdown 的源文件.
 * @property-read string $runtimePath       放置解析后的 markdown option 文件
 * @property-read StorageMeta|null $docStorage
 * @property-read StorageMeta|null $sectionStorage
 * @property-read StorageMeta|null $docInitialStorage
 * @property-read StorageMeta|null $sectionInitialStorage
 *
 */
class MarkdownComponent extends AComponentOption
{
    /**
     * @var MDGroupOption[]|null
     */
    protected $_groupMap;

    public static function stub(): array
    {
        return [

            'reset' => CommuneEnv::isResetRegistry(),
            'langDir' => __DIR__ . '/resources/trans',
            'groups' => [
                [
                    'groupName' => 'Demo',
                    'relativePath' => 'demo',
                    // 命名空间 + 文件的相对路径 = document id
                    'namespace' => 'md.demo',
                ]

            ],

            // 可选配置
            'runtimePath' => StringUtils::gluePath(
                CommuneEnv::getRuntimePath(),
                'markdown'
            ),
            'resourcePath' => realpath(__DIR__ . '/../../demo/resources/markdown'),
            'docStorage' => null,
            'docInitialStorage' => null,
            'sectionStorage' => null,
            'sectionInitialStorage' => null,
        ];
    }

    public static function relations(): array
    {
        return [
            'groups[]' => MDGroupOption::class,
            'docStorage' => StorageMeta::class,
            'docInitialStorage' => StorageMeta::class,
            'sectionStorage' => StorageMeta::class,
            'sectionInitialStorage' => StorageMeta::class,
        ];
    }


    public function bootstrap(App $app): void
    {
        // 注册两个 option 仓库.
        $registry = $app->getServiceRegistry();
        $registry->registerConfigProvider(
                new MDOptRegistryProvider([
                    'runtimePath' => $this->runtimePath,
                    'docStorage' => null,
                    'docInitialStorage' => null,
                    'sectionStorage' => null,
                    'sectionInitialStorage' => null,
                ]),
                false
            );


        // 注册所有的 group 组件.
        foreach ($this->groups as $group) {
            $registry->registerProcProvider(
                new MDGroupContextLoader([
                    'id' => $group->groupName,
                    'forceUpdate' => $this->reset,
                    'resourcePath' => $this->resourcePath,
                    'group' => $group,
                ]),
                false
            );
        }

        $this->loadTranslation(
            $app,
            $this->langDir,
            true,
            $this->reset
        );

        $this->dependComponent($app, TreeComponent::class);
    }

    public function getGroupOptionByName(string $groupName) : ? MDGroupOption
    {
        if (!isset($this->_groupMap)) {
            $this->_groupMap = [];
            foreach ($this->groups as $group) {
                $this->_groupMap[$group->groupName] = $group;
            }
        }

        return $this->_groupMap[$groupName] ?? null;
    }


    public function __destruct()
    {
        unset(
            $this->_groupMap
        );
        parent::__destruct();
    }

}