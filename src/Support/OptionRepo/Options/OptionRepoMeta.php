<?php


namespace Commune\Chatbot\Config\Options;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Commune\Support\OptionRepo\Options\StorageMeta;


/**
 * OptionRepo 是一个配置中心的抽象层.
 * 系统通过这个配置中心, 从介质中读取配置.
 *
 * 当前类则用于定义配置中心的源数据.
 *
 *
 * @property-read string $optionClazz
 * storage 里存储的 option的名称.
 *
 * @property-read array[] $constants
 * 作为常量预加载的option. 不可被修改.
 *
 * @property-read StorageMeta $rootStorage
 * 根 storage. 所有 storage 节点的数据以它为准.
 *
 * @property-read StorageMeta[] $storagePipeline
 * 获取数据的中间层.
 * 读取数据时, 从上往下读, 读到任何合法数据则返回.
 * 更新数据时数据从根节点往上同步.
 */
class OptionRepoMeta extends Option
{
    const IDENTITY = 'optionClazz';

    protected static $associations = [
        'rootStorage' => StorageMeta::class,
        'storagePipeline[]' =>  StorageMeta::class,
    ];

    public static function stub(): array
    {
        return [
            'optionClazz' => '',

            'rootStorage' => null,

            'storagePipeline' => [

            ],

            'constants' =>  [

            ],
        ];
    }

    public function newOption(array $data) : Option
    {
        $className = $this->optionClazz;
        return new $className($data);
    }

    public static function validate(array $data): ? string
    {
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

        $driver = $data['rootStorage']['driver'];

        if (!is_a($driver, RootOptionStage::class, TRUE)) {
            return 'root storage driver must be instance of ' . RootOptionStage::class . ", $driver given";
        }

        return null;
    }

}