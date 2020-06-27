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
use Commune\Platform\Shell\Stdio\StdioSinglePlatform;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioSinglePlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'concrete' => StdioSinglePlatform::class,
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