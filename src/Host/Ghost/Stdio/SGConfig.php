<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio;

use Commune\Blueprint\Configs\GhostConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGConfig extends GhostConfig
{
    public static function stub(): array
    {
        return parent::stub();
    }

}