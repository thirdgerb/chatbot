<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Log;

use Commune\Framework\Contracts\LogInfo;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ILogInfo implements LogInfo
{
    public function bootStartKeyStep(string $methodName): string
    {
        return "boot $methodName start";
    }

    public function bootEndKeyStep(string $methodName): string
    {
        return "boot $methodName end";
    }

    public function bootRegisterExistsProvider(string $id): string
    {
        return "registering service provider $id already exists";
    }

    public function bootRegisterInvalidProvider(string $providerClass, string $validation = ''): string
    {
        return "register service provider $providerClass fail: $validation";
    }

    public function bootDoBootProvider(string $id): string
    {
        return "boot service provider $id";
    }


    public function bootRegisterProvider(string $id): string
    {
        return "register service provider $id";
    }

    public function bootInvalidProviderConfiguration($index, $value): string
    {
        return "registering service provider invalid. index: "
            . var_export($index, true)
            . ', value: '
            . var_export($value, true);
    }

    public function bootContractNotBound(string $abstract): string
    {
        return "core contract $abstract not bound";
    }

    public function bootShellNotDefined(string $shell): string
    {
        return "shell $shell not defined";
    }

    public function appReceiveInvalidRequest(string $message): string
    {
        return "invalid request: $message";
    }

    public function sessionPipelineLog(): string
    {
        return 'end session pipe';
    }


}