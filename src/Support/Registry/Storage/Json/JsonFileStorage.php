<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Storage\Json;

use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Storage\AbsFileStorage;
use Commune\Support\Registry\Storage\FileStorageOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JsonFileStorage extends AbsFileStorage
{
    protected $ext = 'json';

    /**
     * @param array $option
     * @param JsonStorageOption $meta
     * @return string
     */
    protected function parseArrayToString(array $option, FileStorageOption $meta): string
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

    protected function isValidOption(StorageOption $option): bool
    {
        return $option instanceof JsonStorageOption;
    }


}