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

use Commune\Blueprint\Ghost\MindMeta\EntityMeta;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Storage\AbsFileStorageDriver;
use Commune\Support\Registry\Storage\FileStorageOption;
use Commune\Support\Utils\TypeUtils;
use Symfony\Component\Yaml\Yaml;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class YmlFileStorageDriver extends AbsFileStorageDriver
{
    protected $ext = 'yml';

    /**
     * @param array $option
     * @param YmlStorageOption $meta
     * @return string
     */
    protected function parseArrayToString(array $option, FileStorageOption $meta): string
    {
        return Yaml::dump($option, $meta->inline, $meta->intent);
    }

    protected function parseStringToArray(string $content): array
    {
        $data = Yaml::parse($content);
        return $data ?? [];
    }

    protected function isValidOption(StorageOption $option): bool
    {
        return $option instanceof YmlStorageOption;
    }

}