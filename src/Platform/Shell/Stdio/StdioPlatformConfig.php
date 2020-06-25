<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Stdio;

use Commune\Platform\IPlatformConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'stdio',
            'concrete' => StdioPlatform::class,
            'adapter' => StdioAdapter::class,
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [],
            'options' => [
                StdioOption::class => [],
            ],
        ];
    }

}