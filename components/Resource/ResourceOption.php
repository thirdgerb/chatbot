<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Resource;

use Commune\Support\Option\AbsOption;
use Commune\Support\Registry\Storage\FileStorageOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name          既是 resources 的名称, 也代表相对路径.
 * @property-read string $optionClass   既是 optionClass, 也通常是 category
 * @property-read bool $isDir           目标是路径, 还是文件.
 *
 * @property-read string $loader
 *
 *
 * @property-read string $category      如果目标 category 不等于 optionClass 时再填
 */
class ResourceOption extends AbsOption
{
    const LOADER_PHP = FileStorageOption::OPTION_PHP;
    const LOADER_YML = FileStorageOption::OPTION_YML;
    const LOADER_JSON = FileStorageOption::OPTION_JSON;



    public static function stub(): array
    {
        return [
            'name' => '',
            'optionClass' => '',
            'isDir' => true,
            'loader' => self::LOADER_PHP,
            'category' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}