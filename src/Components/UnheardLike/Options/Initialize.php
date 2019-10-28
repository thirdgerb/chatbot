<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Support\Option;

/**
 * 剧本启动时的状态
 *
 * @property-read string $time  分:秒:厘  起点所处的时间.
 * @property-read string $follow 玩家在起点时跟随的对象.
 */
class Initialize extends Option
{
    public static function stub(): array
    {
        return [
            'time' => '',
            'follow' => '',
        ];
    }

    public static function validate(array $data): ? string
    {
        foreach (static::stub() as $key => $value) {
            if (!isset($data[$key])) {
                return "$key is required";
            }
        }
        return null;
    }

}