<?php


namespace Commune\Support\OptionRepo\Storage\Json;


use Commune\Support\Option;
use Symfony\Component\Yaml\Yaml;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class YamlRootStorage extends RootFileStorage
{
    protected $ext = 'yaml';

    protected function parseOptionToString(Option $option): string
    {
        return Yaml::dump($option->toArray());
    }

    protected function parseStringToOption(string $optionName, string $content): ? Option
    {
        $data = Yaml::parse($content);
        return !empty($data) && is_array($data)  ? new $optionName($data) : null;
    }


}