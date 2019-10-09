<?php


namespace Commune\Support\OptionRepo\Options;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionStorage;

/**
 * 定义一个 option Storage
 * 通常还需要定义子类, 以实现额外的参数定义.
 *
 * @property-read string $name 当前storage 的名称. 日志里常用.
 * @property-read string $driver storage 的驱动. 通常要在容器里绑定.
 */
class StorageMeta extends Option
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'driver' => '',
        ];
    }

    public static function validate(array $data): ? string
    {
        if (
            !isset($data['driver'])
            || !is_string($data['driver'])
            || !is_a($data['driver'], OptionStorage::class, TRUE)
        ) {
            return "storage driver {$data['driver']} is invalid";
        }

        return parent::validate($data);
    }

}