<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Demo\Config\Shell;

use Commune\Shell\IShellConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoShellConfig extends IShellConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'name' => 'demo',
            'providers' => [],
            'options' => [],
            'components' => [],
            'protocals' => [],
            'sessionExpire' => 864000,
            'sessionLockerExpire' => 0,
        ];
    }

}