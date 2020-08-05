<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Log;

use Commune\Contracts\Log\LogInfo;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ILogInfo implements LogInfo
{
    public function bootingStartKeyStep(string $methodName): string
    {
        return "key step start: $methodName";
    }

    public function bootingEndKeyStep(string $methodName): string
    {
        return "key step end: $methodName";
    }

    public function bootingStartBootstrapper(string $bootstrapper): string
    {
        return "bootstrapper start: $bootstrapper";
    }

    public function bootingEndBootstrapper(string $bootstrapper): string
    {
        return "bootstrapper end: $bootstrapper";
    }


    public function bootingRegisterExistsProvider(string $id, string $exists, string $challenger): string
    {
        return "registering provider id [$id] already registered by $exists, challenger is $challenger";
    }

//    public function bootRegisterInvalidProvider(string $providerClass, string $validation = ''): string
//    {
//        return "register service provider $providerClass fail: $validation";
//    }
//
    public function bootingBootProvider(string $id, string $name): string
    {
        return "\nboot provider: $name;\nid: $id";
    }

    public function bootingRegisterProviderWarning(string $providerId, string $except, string $given): string
    {
        return "register service provider $providerId at scope $given, $except expected";
    }


    public function bootingRegisterProvider(string $id, string $name): string
    {
        return "\nregister provider: $name;\nid: $id";
    }

    public function bootContractNotBound(string $abstract): string
    {
        return "core contract $abstract not bound";
    }

    public function bootingRegisterConfigOption(string $optionName): string
    {
        return "register config option, name $optionName";
    }

    public function bootingRegisterComponent(string $id, string $by = null): string
    {
        $by = isset($by) ? ", by $by" : '';
        return "register component: $id$by";
    }


    public function bootingBootComponent(string $appType, string $componentId): string
    {
        return "boot component: $componentId for $appType";
    }



//    public function bootShellNotDefined(string $shell): string
//    {
//        return "shell $shell not defined";
//    }

//    public function appReceiveInvalidRequest(string $message): string
//    {
//        return "invalid request: $message";
//    }

//    public function sessionPipelineLog(): string
//    {
//        return 'end session pipe';
//    }

    public function bootingUnInstancedReqContainer(): string
    {
        return 'request container is not instanced';
    }

}