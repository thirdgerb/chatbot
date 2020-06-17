<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Demo\Config\Platform;

use Commune\Platform\IPlatformConfig;
use Commune\Platform\StdioDemo\StdioDemoOption;
use Commune\Platform\StdioDemo\StdioDemoPlatform;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioPlatformConfig extends IPlatformConfig
{
    public static function stub(): array
    {
        return [
            'id' => 'stdio',
            'concrete' => StdioDemoPlatform::class,
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [],
            'options' => [
                StdioDemoOption::class => [
                    'shellName' => 'stdioTestName',
                    'guestId' => 'stdioTestGuestId',
                    'guestName' => 'stdioTestGuestName',
                ],
            ],
        ];
    }

}