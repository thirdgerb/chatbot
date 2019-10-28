<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Support\Option;

/**
 *
 * @property-read string $toContinue 表示继续的标记
 * @property-read string $calling 唤醒训练师
 * @property-read string $quit 退出游戏.
 * @property-read string $rewind
 * @property-read string $mark
 * @property-read string $answer
 * @property-read string $follow
 * @property-read string $back
 * @property-read string setTime
 */
class Commands extends Option
{
    public static function stub(): array
    {
        return [
            'toContinue' => '.',
            'calling' => '九号',
            'quit' => '退出',
            'rewind' => '从头开始',
            'mark' => '标注',
            'answer' => '回答',
            'follow' => '跟随',
            'setTime' => '选择时间',
            'back' => '返回',
        ];
    }


}