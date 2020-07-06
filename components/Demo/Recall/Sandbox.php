<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Recall;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Ghost\Memory\AbsRecall;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property int $test
 * @property int $test1
 */
class Sandbox extends AbsRecall
{
    public static function __scopes(): array
    {
        return [
            ClonerScope::GUEST_ID,
        ];
    }

    public static function __attrs(): array
    {
        return [
            'test' => 0,
            'test1' => 0,
        ];
    }
}