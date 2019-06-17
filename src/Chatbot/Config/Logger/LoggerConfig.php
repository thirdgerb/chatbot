<?php


namespace Commune\Chatbot\Config\Logger;


use Commune\Support\Option;
use Psr\Log\LogLevel;

/**
 * @property-read string $name 日志名
 * @property-read string $path
 * @property-read int $days  为0 表示不轮换, 否则会按日换文件.
 * @property-read string $level 日志级别.
 * @property-read bool $bubble 是否冒泡到别的handler
 * @property-read bool|null $permission 文件的权限设置
 * @property-read bool $locking 是否文件锁
 *
 */
class LoggerConfig extends Option
{
    public static function stub(): array
    {
        return [
            'name' => 'chatbot',
            'path' => __DIR__,
            'days' => 7,
            'level' => LogLevel::DEBUG,
            'bubble' => true,
            'permission' => null,
            'locking' => false,
        ];
    }


}