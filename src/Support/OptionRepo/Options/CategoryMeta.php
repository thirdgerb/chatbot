<?php


namespace Commune\Support\OptionRepo\Options;

use Commune\Support\Struct;

/**
 * OptionRepo 是一个配置中心的抽象层.
 * 系统通过这个配置中心, 从介质中读取配置.
 *
 * 当前类则用于定义配置中心的源数据.
 *
 * @property-read string $name 仓库的唯一ID
 *
 * @property-read string $optionClazz
 * storage 里存储的 option的名称.
 *
 * @property-read array[] $constants
 * 作为常量预加载的option. 不可被修改.
 *
 * @property-read MetaHolder $rootStorage
 * 根 storage. 所有 storage 节点的数据以它为准.
 *
 * @property-read MetaHolder[] $storagePipeline
 * 获取数据的中间层.
 * 读取数据时, 从上往下读, 读到任何合法数据则返回.
 * 更新数据时数据从根节点往上同步.
 */
class CategoryMeta extends Option
{
    const IDENTITY = 'name';

    protected static $associations = [
        'rootStorage' => MetaHolder::class,
        'storagePipeline[]' =>  MetaHolder::class,
    ];

    public static function stub(): array
    {
        return [
            'name' => '',

            'optionClazz' => '',

            'rootStorage' => null,

            'storagePipeline' => [

            ],

            'constants' =>  [

            ],
        ];
    }


    public static function validate(array $data): ? string
    {
        if (empty($data['name'])) {
            return 'category name should not be empty';
        }

        if (empty($data['optionClazz'])) {
            return 'optionClazz should not be empty';
        }

        $clazz = $data['optionClazz'];
        if (!is_string($clazz) || !is_a($clazz, Option::class, TRUE)) {
            return 'invalid option clazz';
        }

        $identity = constant("$clazz::IDENTITY");
        if (empty($identity)) {
            return 'invalid option clazz which has no identity';
        }

        if (empty($data['rootStorage'])) {
            return 'root storage must not be empty';
        }

        return null;
    }


    public function getRootStorage() : StorageMeta
    {
        return $this->rootStorage->getStorageMeta();
    }

    /**
     * @return StorageMeta[]
     */
    public function getStoragePipeline() : \Generator
    {
        foreach ($this->storagePipeline as $metaHolder) {
            yield $metaHolder->getStorageMeta();
        }
    }

}