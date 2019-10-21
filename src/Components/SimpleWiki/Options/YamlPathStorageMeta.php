<?php


namespace Commune\Components\SimpleWiki\Options;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;

/**
 *
 * @property-read int $inline yaml 序列化时, 第几层会压缩成单行
 * @property-read int $intent 每次缩进的长度.
 * @property-read string $name storage 名
 * @property-read string $path 当前配置存储的文件. 一个文件里只应该有一种option
 * @property-read bool $isDir path是文件名, 还是路径名. 如果是路径名, 会用option id 做文件名.
 * @property-read int|int[]|string|string[] $depth 目录搜索的深度. @see Finder::depth()
 */
class YamlPathStorageMeta extends YamlStorageMeta
{

    public function newOption(string $className, array $data, string $path): Option
    {
        $path = str_replace(realpath($this->path), '', realpath($path));
        $path = trim( $path, DIRECTORY_SEPARATOR);
        $intentName = WikiOption::INTENT_NAME_PREFIX
            . '.'
            . str_replace(DIRECTORY_SEPARATOR, '.', $path);
        // 去掉 .ext
        $intentNameSecs = explode('.', $intentName);
        array_pop($intentNameSecs);
        $intentName = implode('.', $intentNameSecs);

        $data['intentName'] = $intentName;
        return parent::newOption($className, $data, $path);
    }

}