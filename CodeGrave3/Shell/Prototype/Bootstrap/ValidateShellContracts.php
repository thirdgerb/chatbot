<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Bootstrap;

use Commune\Chatbot\InjectableDependencies;
use Commune\Framework\Prototype\Bootstrap\ContractsValidator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ValidateShellContracts extends ContractsValidator
{
    public function getProcBindings(): array
    {
        return array_merge(
            InjectableDependencies::CHATBOT_BASIC_BINDINGS,
            InjectableDependencies::APP_BASIC_BINDINGS,
            InjectableDependencies::SHELL_PROCESS_LEVEL
        );
    }

    public function getReqBindings(): array
    {
        return array_merge(
            InjectableDependencies::REQUEST_LEVEL,
            InjectableDependencies::SHELL_REQUEST_LEVEL
        );
    }


}