<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Blueprint\Ghost\Auth\Supervise;
use Commune\Support\Alias\AbsAliases;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AliasesForAuth extends AbsAliases
{
    const SUPERVISE = 'supervise';

    public static function preload(): void
    {
        self::setAlias( Supervise::class, self::SUPERVISE);
    }


}