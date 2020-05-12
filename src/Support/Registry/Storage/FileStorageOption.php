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
use Symfony\Component\Finder\Finder;


/**
 * 用本地文件保存 option 配置的 storage
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name  storage 名
 *
 * @property-read string $path  文件存储路径. 一个文件里只应该有一种option
 *
 * @property-read bool $isDir   path是文件名, 还是路径名. 如果是路径名, 会把文件的相对路径作为 option Id
 *
 * @property-read mixed $depth  目录搜索的深度.
 *
 * @see Finder::depth()
 */
abstract class FileStorageOption extends StorageOption
{
    public static function stub(): array
    {
        return [
            'name' => static::class,
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
        if (empty($data['name'])) {
            return 'name field is required';
        }

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

        $driver = $data['driver'] ?? '';

        if (!is_a($driver, $abs = AbsFileStorage::class, TRUE)) {
            return "driver must be subclass of $abs, $driver given";
        }

        return parent::validate($data);
    }

}