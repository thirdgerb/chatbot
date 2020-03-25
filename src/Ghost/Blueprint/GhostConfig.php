<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Support\Structure;


/**
 * 机器人灵魂的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $defaultComprehension
 */
class GhostConfig extends Structure
{
    public static function stub(): array
    {
        return [];
    }


}