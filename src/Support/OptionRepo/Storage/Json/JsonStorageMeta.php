<?php


namespace Commune\Support\OptionRepo\Storage\Json;


use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\OptionRepo\Storage\FileStorageMeta;

/**
 * @mixin FileStorageMeta
 * @property-read int $jsonOption json encode 时的option配置.
 */
class JsonStorageMeta extends FileStorageMeta
{
    const DRIVER = JsonRootStorage::class;

    public static function stub(): array
    {
        $data = parent::stub();
        $data['jsonOption'] = ArrayAndJsonAble::PRETTY_JSON;
        return $data;
    }
}