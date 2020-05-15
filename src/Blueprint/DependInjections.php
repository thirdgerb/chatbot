<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Framework;
use Commune\Contracts\Log;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DependInjections
{

    const BASIC_APP_BINDINGS = [
        Framework\ProcContainer::class,
        Framework\ServiceRegistrar::class,
        Log\ConsoleLogger::class,
        Log\LogInfo::class,
    ];

    const GHOST_PROC_BINDINGS = [

    ];

    const GHOST_REQ_BINDINGS = [

    ];
}