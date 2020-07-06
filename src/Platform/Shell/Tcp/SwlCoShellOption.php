<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Support\Swoole\ServerOption;
use Commune\Platform\Libs\SwlCo\TcpPlatformOption;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read ServerOption $poolOption
 * @property-read TcpPlatformOption $adapterOption
 */
class SwlCoShellOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'poolOption' => [],
            'adapterOption' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'poolOption' => ServerOption::class,
            'adapterOption' => TcpPlatformOption::class,
        ];
    }


}