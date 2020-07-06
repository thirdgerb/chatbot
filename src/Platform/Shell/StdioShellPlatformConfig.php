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
use Commune\Platform\Libs\Stdio\StdioClientOption;
use Commune\Platform\Shell\Stdio\StdioShellPlatform;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'concrete' => StdioShellPlatform::class,
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [
            ],
            'options' => [
                StdioClientOption::class,
            ],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields(
                $data,
                ['id', 'bootShell']
            )
            ?? parent::validate($data);
    }
}