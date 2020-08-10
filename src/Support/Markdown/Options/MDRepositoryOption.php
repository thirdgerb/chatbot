<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown\Options;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $path
 */
class MDRepositoryOption extends AbsOption
{
    public static function stub(): array
    {
        return [

        ];
    }

    public static function relations(): array
    {
        return [];
    }


}