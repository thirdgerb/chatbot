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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Contracts;
use Commune\Protocals;
use Commune\Blueprint\Framework;
use Commune\Blueprint\Configs;
use Commune\Support\Registry\OptRegistry;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DependInjections
{

    const BASIC_APP_BINDINGS = [
        Framework\ProcContainer::class,
        Framework\ServiceRegistrar::class,
        Contracts\Log\ConsoleLogger::class,
        Contracts\Log\LogInfo::class,
    ];

    const APP_PROC_BINDINGS = [
        Framework\ProcContainer::class,
        Contracts\Log\ExceptionReporter::class,
        OptRegistry::class,
        LoggerInterface::class,
    ];

    /*------- ghost -------*/

    const GHOST_PROC_BINDINGS = [
        Ghost::class,
        Configs\GhostConfig::class,
        Mindset::class,
    ];

    const GHOST_REQ_BINDINGS = [
        Contracts\Cache::class,
        Ghost\Cloner\ClonerScene::class,
        Ghost\Cloner\ClonerScope::class,
        Ghost\Cloner\ClonerLogger::class,

        Ghost\Cloner\ClonerStorage::class,
        Ghost\Runtime\Runtime::class,
        Ghost\Auth\Authority::class,
    ];

    const GHOST_REQ_INSTANCES = [
        Cloner::class,
        Framework\Session::class,
        Framework\ReqContainer::class,
        Protocals\Intercom\GhostInput::class,
        Protocals\HostMsg::class,
        Protocals\Comprehension::class,

    ];
}