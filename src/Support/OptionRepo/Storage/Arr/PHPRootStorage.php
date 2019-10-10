<?php


namespace Commune\Support\OptionRepo\Storage\Arr;


use Commune\Support\OptionRepo\Storage\FileStorageMeta;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class PHPRootStorage extends RootFileStorage
{
    protected $ext = 'php';

    protected function parseArrayToString(array $option, FileStorageMeta $meta): string
    {
        $arr =  var_export($option, true);
        return <<<EOF
<?php

return $arr;
EOF;

    }

    protected function readFileArr(string $path): array
    {
        return include $path;
    }

    protected function parseStringToArray(string $content): array
    {
        return [];
    }


}