<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo;

use Commune\Platform\IPlatformConfig;
use Commune\Platform\SwooleCo\TcpGhost\SCTGhostOption;
use Commune\Platform\SwooleCo\TcpGhost\SCTGhostPlatform;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpGhostPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [

            'id' => 'tcpGhost',
            'concrete' => SCTGhostPlatform::class,

            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [],
            'options' => [
                SCTGhostOption::class => [
                    'adapterOption' => [

                    ],
                    'poolOption' => [

                    ]
                ],
            ],
        ];
    }

}