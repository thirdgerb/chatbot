<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Meta;

use Commune\Support\Option\AbsMeta;
use Commune\Support\Option\Wrapper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $wrapper
 * @property-read array $config
 *
 * @method StorageOption getWrapper()
 */
class StorageMeta extends AbsMeta
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'wrapper' => '',
            'config' => [],
        ];
    }

    static function validateWrapper(string $wrapper): ? string
    {
        return is_a($wrapper, StorageOption::class, TRUE)
            ? null
            : 'wrapper must be subclass of ' . StorageOption::class;
    }

    public static function relations(): array
    {
        return [];
    }


}