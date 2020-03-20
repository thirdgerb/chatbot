<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * @property-read string $id 选项的内容名
 * @property-read string $option 暴露给用户的选项名.
 * @property-read string $intent 可选, 允许用意图来匹配选项.
 * @property-read array $ifItem 允许这个选项的条件.
 * @property-read array $getItem 得到道具.
 * @property-read string $to 导航的目标 stage
 */
class ChoiceOption extends Option
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'option' => '',
            'intent' => '',
            'ifItem' => [
            ],
            'getItem' => [
            ],
            'to' => '',
        ];
    }


}