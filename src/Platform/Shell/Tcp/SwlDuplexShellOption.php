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

use Commune\Support\Option\AbsOption;
use Commune\Support\Swoole\ServerOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $adapterName
 *
 * @property-read int $tableSize
 *
 * @property-read ServerOption $serverOption
 *
 */
class SwlDuplexShellOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'adapterName' => SwlCoTextShellAdapter::class,
            'tableSize' => 10000,
            'serverOption' => [
                'host' => '127.0.0.1',
                'port' => '9503',

            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'serverOption' => ServerOption::class,
        ];
    }


}