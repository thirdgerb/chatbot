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

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $path      文件存储路径. 一个文件里只应该有一种option
 *
 * @property-read bool $isDir       path是文件名, 还是路径名. 如果是路径名, 会把文件的相对路径作为 option Id
 *
 * @property-read mixed $depth      目录搜索的深度.
 * @see Finder::depth()
 *
 * @property-read int $jsonOption   保存 option 的格式.
 */
class JsonStorageOption extends FileStorageOption
{
    public static function stub(): array
    {
        return [
            'path' => '',
            'isDir' => true,
            'depth' => 0,
            'jsonOption' => ArrayAndJsonAble::PRETTY_JSON
        ];
    }

    public function getDriver(): string
    {
        return JsonFileStorage::class;
    }


}