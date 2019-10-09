<?php


namespace Commune\Support\OptionRepo\Storage\Memory;


use Commune\Support\OptionRepo\Options\StorageMeta;


/**
 * 内存缓存的配置.
 *
 * @property-read int $expire 过期时间.
 */
class MemoryStorageMeta extends StorageMeta
{

    public static function stub(): array
    {
        return [
            'name' => '',
            'driver' => '',
            'expire' => 0,
        ];
    }


}