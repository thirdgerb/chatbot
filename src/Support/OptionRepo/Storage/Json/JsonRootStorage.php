<?php


namespace Commune\Support\OptionRepo\Storage\Json;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class JsonRootStorage extends RootFileStorage
{
    protected $ext = 'json';

    protected function parseOptionToString(Option $option): string
    {
        return $option->toPrettyJson();
    }

    protected function parseStringToOption(string $optionName, string $content): ? Option
    {
        $data = json_decode($content, true);
        return !empty($data) && is_string($data) ? new $optionName($data) : null;
    }


}