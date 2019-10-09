<?php


namespace Commune\Support\OptionRepo\Storage;


use Commune\Support\OptionRepo\Options\StorageMeta;


/**
 * @property-read string $name storage 名
 * @property-read string $driver storage driver
 * @property-read string $directory 当前配置存储的路径. 一个路径下只应该有一种option
 * @property-read bool $caching 是否缓存, 然后不变
 */
abstract class FileStorageMeta extends StorageMeta
{
    const DRIVER = '';

    public static function stub(): array
    {
        return [
            'name' => '',
            'driver' => static::DRIVER,
            'directory' => '',
            'caching' => true,
        ];
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['name'])) {
            return 'name field is required';
        }

        if (empty($data['directory']) || !is_dir($data['directory'])) {
            return 'directory is invalid';
        }

        return parent::validate($data);
    }


}