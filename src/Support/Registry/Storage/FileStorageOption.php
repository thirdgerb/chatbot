<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Storage;

use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Registry\Storage\PHP\PHPStorageOption;
use Commune\Support\Registry\Storage\Yaml\YmlStorageOption;


/**
 * 用本地文件保存 option 配置的 storage
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $path  文件存储路径. 一个文件里只应该有一种option
 *
 * @property-read bool $isDir   判断 path 是路径名还是文件名. 如果是路径, 则每个 option 存放一个单独的文件. 否则所有的 option 存放在同一个文件中.
 *
 */
abstract class FileStorageOption extends StorageOption
{

    const OPTION_PHP = 'php';
    const OPTION_JSON = 'json';
    const OPTION_YML = 'yml';

    const OPTIONS = [
        self::OPTION_JSON => JsonStorageOption::class,
        self::OPTION_PHP => PHPStorageOption::class,
        self::OPTION_YML => YmlStorageOption::class,
    ];

    public static function stub(): array
    {
        return [
            'path' => '',
            'isDir' => true,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['path'])) {
            return 'path field is required';
        }

        $path = $data['path'];
        $dir = $data['isDir'] ?? false;

        if (!is_bool($dir)) {
            return 'isDir should be bool value';
        }

        if ($dir && !is_dir($path)) {
            return "resource directory path $path not exits";

        } elseif (!$dir && !is_dir(dirname($path))) {
            return "resource file $path dir not exits";
        }

        return parent::validate($data);
    }

}