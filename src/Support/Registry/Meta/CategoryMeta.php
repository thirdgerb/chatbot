<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Meta;

use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Option;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name              分类的名称. 同一个名称下可以有多个分类.
 *
 * @property-read string $optionClass       storage 里存储的 option的类名

 * @property-read StorageMeta $rootStorage
 * 根 storage. 所有 storage 节点的数据以它为准.
 *
 * @property-read StorageMeta[] $storagePipeline
 *
 * 获取数据的中间层.
 * 读取数据时, 从上往下读, 读到任何合法数据则返回.
 * 更新数据时数据从根节点往上同步.
 *
 *
 */
class CategoryMeta extends AbsOption
{
    const IDENTITY = 'name';

    protected static $associations = [
        'rootStorage' => StorageMeta::class,
        'storagePipeline[]' => StorageMeta::class,
    ];

    public static function stub(): array
    {
        return [
            'name' => '',
            'optionClass' => '',
            'rootStorage' => [],
            'storagePipeline' => [],
        ];
    }


    public static function validate(array $data): ? string
    {
        if (empty($data['name'])) {
            return 'category name should not be empty';
        }

        if (empty($data['optionClass'])) {
            return 'optionClass should not be empty';
        }

        $class = $data['optionClass'];
        if (!is_string($class) || !is_a($class, Option::class, TRUE)) {
            return 'invalid option class';
        }

        $identity = constant("$class::IDENTITY");
        if (empty($identity)) {
            return 'invalid option class which has no identity';
        }

        if (empty($data['rootStorage'])) {
            return 'root storage must not be empty';
        }

        return null;
    }

}