<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Storage\Yaml;

use Commune\Support\Registry\Storage\FileStorageOption;
use Symfony\Component\Finder\Finder;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name storage 名
 * @property-read string $path 当前配置存储的文件. 一个文件里只应该有一种option
 * @property-read bool $isDir path是文件名, 还是路径名. 如果是路径名, 会用option id 做文件名.
 * @property-read int|int[]|string|string[] $depth 目录搜索的深度.
 * @see Finder::depth()
 *
 * @property-read int $inline yaml 序列化时, 第几层会压缩成单行
 * @property-read int $intent 每次缩进的长度.
 */
class YmlStorageOption extends FileStorageOption
{
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

    public function getDriver(): string
    {
        return YmlFileStorage::class;
    }


}