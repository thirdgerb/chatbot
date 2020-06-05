<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Log;

/**
 * 把系统级的日志表达做成类
 * 方便有些日志记得不好, 改都没地方改.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface LogInfo
{

    /*------ boot ------*/


    public function bootingStartKeyStep(string $methodName) : string;

    public function bootingEndKeyStep(string $methodName) : string;

    public function bootingStartBootstrapper(string $bootstrapper) : string;

    public function bootingEndBootstrapper(string $bootstrapper) : string;

    public function bootingRegisterExistsProvider(string $id) : string;

    public function bootingRegisterProvider(string $id) : string;

    public function bootingBootProvider(string $id) : string;

    public function bootingRegisterProviderWarning(string $providerId, string $except, string $given) : string;

    public function bootingUnInstancedReqContainer() : string;

    public function bootingRegisterConfigOption(string $optionName) : string;


    public function bootContractNotBound(string $abstract) : string;

    public function bootingBootComponent(string $appType, string $componentId) : string;

    public function bootingRegisterComponent(string $id, string $by = null) : string;
//    public function bootShellNotDefined(string $shell) : string;

    /*------ app info ------*/

//    public function appReceiveInvalidRequest(string $message) : string;

    /*------ session ------*/

//    public function sessionPipelineLog() : string;

    /*------ shell ------*/


    /*------ logic ------*/
}