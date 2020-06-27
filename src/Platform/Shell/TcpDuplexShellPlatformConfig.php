<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell;

use Commune\Platform\IPlatformConfig;
use Commune\Platform\Libs\SwlAsync\SwlAsyncBroadcastAdapter;
use Commune\Platform\Shell\Tcp\SwlAsyncShellOption;
use Commune\Platform\Shell\Tcp\SwlAsyncShellPlatform;
use Commune\Platform\Shell\Tcp\SwlAsyncTextShellAdapter;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpDuplexShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'concrete' => SwlAsyncShellPlatform::class,
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [

            ],
            'options' => [
                SwlAsyncShellOption::class => [
                    'adapterName' => SwlAsyncTextShellAdapter::class,
                    'tableSize' => 10000,
                    'serverOption' => [
                        'host' => '127.0.0.1',
                        'port' => '9503',

                    ],
                ],
            ],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['bootShell'])
            ?? parent::validate($data);
    }

}