<?php


namespace Commune\Support\OptionRepo\Options;


use Commune\Support\Struct;

/**
 * 定义一个 option Storage
 * 通常还需要定义子类, 以实现额外的参数定义.
 *
 * @property-read string $name 当前storage 的名称. 日志里常用.
 */
abstract class StorageMeta extends Option
{
    const IDENTITY = 'name';

    const DRIVER = '';

    public static function stub(): array
    {
        return [
            'name' => '',
        ];
    }

    public function getDriver() : string
    {
        return static::DRIVER;
    }

}