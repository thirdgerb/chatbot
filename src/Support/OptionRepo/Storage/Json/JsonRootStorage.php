<?php


namespace Commune\Support\OptionRepo\Storage\Json;


use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\OptionRepo\Exceptions\InvalidArgException;
use Commune\Support\OptionRepo\Storage\FileStorageMeta;
use Commune\Support\OptionRepo\Storage\RootFileStorage;

class JsonRootStorage extends RootFileStorage
{
    protected $ext = 'json';

    /**
     * @param array $option
     * @param JSonStorageMeta $meta
     * @return string
     */
    protected function parseArrayToString(array $option, FileStorageMeta $meta): string
    {
        if (empty($option)) {
            return json_encode(new \stdClass());
        }
        return json_encode($option, $meta->jsonOption);
    }

    protected function parseStringToArray(string $content): array
    {
        // 方便暴露问题.
        $data = json_decode($content, true);
        return $data;
    }


}