<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Storage\PHP;

use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Storage\AbsFileStorageDriver;
use Commune\Support\Registry\Storage\FileStorageOption;


/**
 * 使用 php 文件作为配置的值.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class PHPFileStorageDriver extends AbsFileStorageDriver
{
    protected $ext = 'php';

    protected function parseArrayToString(array $optionData, FileStorageOption $storageOption): string
    {
        $arr =  var_export($optionData, true);
        return <<<EOF
<?php

return $arr;
EOF;
    }

    protected function readSingleFile(string $path): array
    {
        return include $path;
    }

    protected function parseStringToArray(string $content): array
    {
        return [];
    }

    protected function isValidOption(StorageOption $option): bool
    {
        return $option instanceof PHPStorageOption;
    }


}