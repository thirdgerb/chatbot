<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 向用户发起确认的事件.
 *
 * @property-read string $query  问题.
 * @property-read array $ifItem 可选. 是否触发的条件.
 * @property-read string $yes  用户确认后进入的stage
 * @property-read string $no 用户反对后, 进入的stage
 */
class ConfirmOption extends Option
{
    const IDENTITY = 'query';

    public static function stub(): array
    {
        return [
            'query' => 'reply.id',
            // 可选, 条件.
            'ifItem' => [
            ],
            'yes' => 'stageName1',
            'no' => 'stageName2',
        ];
    }


}