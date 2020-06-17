<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\StdioDemo;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shellName
 * @property-read string $guestId
 * @property-read string $guestName
 */
class StdioDemoOption extends AbsOption
{

    public static function stub(): array
    {
        return [
            'shellName' => 'stdioTestName',
            'guestId' => 'stdioTestGuestId',
            'guestName' => 'stdioTestGuestName',
        ];
    }

    public static function relations(): array
    {
        return [];
    }
}