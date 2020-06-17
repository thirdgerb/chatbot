<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellConfig extends AbsOption implements ShellConfig
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'providers' => [],
            'options' => [],
            'components' => [],
            'protocals' => [],
            'sessionExpire' => 864000,
            'sessionLockerExpire' => 0,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}