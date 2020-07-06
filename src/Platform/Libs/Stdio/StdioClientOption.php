<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\Stdio;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $creatorName
 * @property-read string $adapter
 * @property-read string $salt
 */
class StdioClientOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'creatorName' => 'stdioClient',
            'adapter' => StdioTextAdapter::class,
            'salt' => 'salt',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}