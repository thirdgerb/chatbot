<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 重定向行为.
 *
 * @property-read array $ifItem 条件, 如果有item并且值为.
 * @property-read string $to 到哪个小节的ID.
 */
class RedirectOption extends Option
{
    const IDENTITY = 'to';

    public static function stub(): array
    {
        return [
            'to' => '',
            'ifItem' => [
                //'itemName' => 'value'
            ],
        ];
    }


}