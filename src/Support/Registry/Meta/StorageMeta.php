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

use Commune\Support\Alias\Aliases;
use Commune\Support\Option\AbsMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $wrapper
 * @property-read array $config
 *
 * @method StorageOption getWrapper()
 */
class StorageMeta extends AbsMeta
{
    const IDENTITY = 'wrapper';

    public static function stub(): array
    {
        return [
            'wrapper' => '',
            'config' => [],
        ];
    }

    static function validateWrapper(string $wrapper): ? string
    {
        $wrapper = Aliases::alias($wrapper);
        return is_a($wrapper, StorageOption::class, TRUE)
            ? null
            : 'wrapper must be subclass of ' . StorageOption::class;
    }

    public static function relations(): array
    {
        return [];
    }


}