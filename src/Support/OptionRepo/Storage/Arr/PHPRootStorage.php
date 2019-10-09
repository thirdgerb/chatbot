<?php


namespace Commune\Support\OptionRepo\Storage\Arr;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class PHPRootStorage extends RootFileStorage
{
    protected $ext = 'php';

    protected function parseOptionToString(Option $option): string
    {
        $string = var_export($option->toArray(), true);
        return <<<EOF
<?php

return $string;
EOF;

    }


    protected function readOption(string $path, string $optionName) : ? Option
    {
        $data = include $path;

        return !empty($data) && is_array($data) ? new $optionName($data) : null;
    }

    protected function parseStringToOption(string $optionName, string $content): ? Option
    {
        return null;
    }




}