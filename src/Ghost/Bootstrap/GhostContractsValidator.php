<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Bootstrap;

use Commune\Blueprint\DependInjections;
use Commune\Framework\Bootstrap\ContractsValidator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostContractsValidator extends ContractsValidator
{
    public function getProcBindings(): array
    {
        return array_merge(
            DependInjections::BASIC_APP_BINDINGS,
            DependInjections::APP_PROC_BINDINGS,
            DependInjections::GHOST_PROC_BINDINGS
        );
    }

    public function getReqBindings(): array
    {
        return DependInjections::GHOST_REQ_BINDINGS;
    }


}