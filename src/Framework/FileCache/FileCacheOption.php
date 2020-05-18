<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\FileCache;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $serialized
 */
class FileCacheOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'id' => '',
            'serialized' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}