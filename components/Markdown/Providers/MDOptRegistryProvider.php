<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Providers;

use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Markdown\Data\MDDocumentData;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $runtimePath
 * @property-read StorageMeta|null $docStorage
 * @property-read StorageMeta|null $sectionStorage
 * @property-read StorageMeta|null $docInitialStorage
 * @property-read StorageMeta|null $sectionInitialStorage
 *
 */
class MDOptRegistryProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public static function stub(): array
    {
        return [
            'runtimePath' => '',
            'docStorage' => null,
            'docInitialStorage' => null,
            'sectionStorage' => null,
            'sectionInitialStorage' => null
        ];
    }

    public static function relations(): array
    {
        return [
            'docStorage' => StorageMeta::class,
            'docInitialStorage' => StorageMeta::class,
            'sectionStorage' => StorageMeta::class,
            'sectionInitialStorage' => StorageMeta::class,
        ];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->make(OptRegistry::class);

        // 注册文档仓库
        $registry->registerCategory(new CategoryOption([
            'name' => MDDocumentData::class,
            'optionClass' => MDDocumentData::class,
            'title' => 'markdown 文档仓库',
            'desc' => 'markdown 文档仓库',
            'storage' => $this->docStorage,
            'initialStorage' => $this->getDocInitialStorage(),
        ]), true);

        $registry->registerCategory(new CategoryOption([
            'name' => MDSectionData::class,
            'optionClass' => MDSectionData::class,
            'title' => 'markdown 段落仓库',
            'desc' => 'markdown 段落仓库',
            'storage' => $this->sectionStorage,
            'initialStorage' => $this->getSectionInitialStorage(),
        ]), true);
    }

    protected function getDocInitialStorage() : StorageMeta
    {
        $path = StringUtils::gluePath(
            $this->runtimePath,
            'docs'
        );

        return $this->docInitialStorage
            ?? new StorageMeta([
                'wrapper' => JsonStorageOption::class,
                'config' => [
                    'path' => $path,
                    'isDir' => true,
                ]
            ]);
    }

    protected function getSectionInitialStorage() : StorageMeta
    {
        $path = StringUtils::gluePath(
            $this->runtimePath,
            'sections'
        );

        return $this->sectionInitialStorage
            ?? new StorageMeta([
                'wrapper' => JsonStorageOption::class,
                'config' => [
                    'path' => $path,
                    'isDir' => true,
                ]
            ]);
    }

    public function register(ContainerContract $app): void
    {
    }


}