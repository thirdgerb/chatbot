<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 所有剧情道具的定义.
 * 在这个story体系中, 只支持枚举值定义道具.
 * 玩家可以获得该道具的某一个枚举值.
 *
 * @property-read string $id  道具的ID
 * @property-read string $title 道具的简介
 * @property-read string[] $enums 道具的枚举值.
 */
class ItemOption extends Option
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'enums' => [
                //'true',
            ],

        ];
    }


}