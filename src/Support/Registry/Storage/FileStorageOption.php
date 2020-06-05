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
use Symfony\Component\Finder\Finder;


/**
 * 用本地文件保存 option 配置的 storage
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $path  文件存储路径. 一个文件里只应该有一种option
 *
 * @property-read bool $isDir   path是文件名, 还是路径名. 如果是路径名, 会把文件的相对路径作为 option Id
 *
 * @property-read mixed $depth  目录搜索的深度.
 *
 *
 * @see Finder::depth()
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
            'depth' => 0,
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

        } elseif (!$dir && !file_exists($path)) {
            return "resource file $path not exits";
        }

        return parent::validate($data);
    }

}