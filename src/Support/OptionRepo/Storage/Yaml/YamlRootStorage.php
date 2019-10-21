<?php


namespace Commune\Support\OptionRepo\Storage\Yaml;


use Commune\Support\OptionRepo\Storage\FileStorageMeta;
use Symfony\Component\Yaml\Yaml;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class YamlRootStorage extends RootFileStorage
{
    protected $ext = 'yml';

    /**
     * @param array $option
     * @param YamlStorageMeta $meta
     * @return string
     */
    protected function parseArrayToString(array $option, FileStorageMeta $meta): string
    {
        return Yaml::dump($option, $meta->inline, $meta->intent);
    }

    protected function parseStringToArray(string $content): array
    {
        $data = Yaml::parse($content);
        return $data ?? [];
    }

}