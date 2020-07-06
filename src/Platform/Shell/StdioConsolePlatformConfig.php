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

use Commune\Framework\Providers;
use Commune\Platform\IPlatformConfig;
use Commune\Platform\Shell\Stdio\StdioPlatform;
use Commune\Support\Utils\TypeUtils;

/**
 * 基于 Stdio 实现的单体 Platform.
 * 在 Platform 上同时启动 Shell 与 Ghost.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioConsolePlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'concrete' => StdioPlatform::class,
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [
                // 用数组来做缓存.
                Providers\CacheByArrProvider::class,
                Providers\MessengerFakeByArrProvider::class,
            ],
            'options' => [
            ],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['bootShell', 'bootGhost'])
            ?? parent::validate($data);
    }

}