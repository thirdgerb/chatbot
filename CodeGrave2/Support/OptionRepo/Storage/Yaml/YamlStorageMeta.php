<?php


namespace Commune\Support\OptionRepo\Storage\Yaml;


use Commune\Support\OptionRepo\Storage\FileStorageMeta;

/**
 *
 * @property-read int $inline yaml 序列化时, 第几层会压缩成单行
 * @property-read int $intent 每次缩进的长度.
 * @property-read string $name storage 名
 * @property-read string $path 当前配置存储的文件. 一个文件里只应该有一种option
 * @property-read bool $isDir path是文件名, 还是路径名. 如果是路径名, 会用option id 做文件名.
 * @property-read int|int[]|string|string[] $depth 目录搜索的深度. @see Finder::depth()
 */
class YamlStorageMeta extends FileStorageMeta
{
    const DRIVER = YamlRootStorage::class;

    public static function stub(): array
    {
        return [
            'name' => static::class,
            'path' => '',
            'isDir' => true,
            'depth' => 0,
            'inline' => 6,
            'intent' => 2,
        ];
    }
}