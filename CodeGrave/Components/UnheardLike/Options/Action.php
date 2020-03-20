<?php


namespace Commune\Components\UnheardLike\Options;



use Commune\Support\Option;

/**
 * 一个单元的行动内容.
 *
 * @property-read string $t 当前动作所处的时间. 也是对应的帧ID
 * @property-read string $at 当前动作所处的空间.
 * @property-read string[] $lines 当前动作要发出的信息.
 */
class Action extends Option
{
    const IDENTITY = 't';

    public static function stub(): array
    {
        return [
            't' => '',
            'at' => '',
            'lines' => [
            ],
        ];
    }


}