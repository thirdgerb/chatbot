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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name                  分类的名称. 同一个名称下可以有多个分类.
 * @property-read string $title                 分类的标题.
 * @property-read string $desc                  分类的简介.
 *
 * @property string $optionClass                storage 里存储的 option的类名
 * 
 *
 * @property StorageMeta|null $storage          获取数据的 storage. 如果没有定义, 则使用 initStorage.
 *
 * @property StorageMeta|null $initialStorage   初始化时使用的 Storage
 * 
 * @property bool $temporary                    标记为临时的资源, 备份时不会备份它. 
 *
 */
class CategoryOption extends AbsOption
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'optionClass' => '',
            'title' => '',
            'desc' => '',
            'storage' => null,
            'initialStorage' => null,
            'temporary' => false
        ];
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageMeta::class,
            'initialStorage' => StorageMeta::class
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        if (empty($data['storage']) && empty($data['initialStorage'])) {
            return "storage and init storage could not both empty";
        }
        return parent::validate($data);
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}