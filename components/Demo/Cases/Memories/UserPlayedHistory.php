<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Cases\Memories;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Context\ParamBuilder;
use Commune\Host\Recall\ARecall;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property int $total
 * @property int $highestScore
 */
class UserPlayedHistory extends ARecall
{
    public static function getScopes(): array
    {
        return [ClonerScope::GUEST_ID];
    }

    public static function paramBuilder(ParamBuilder $builder): ParamBuilder
    {
        return $builder
            ->define('total', 0)
            ->define('highestScore', 0);
    }


}