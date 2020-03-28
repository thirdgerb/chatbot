<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;


/**
 * 把系统级的日志表达做成类
 * 方便有些日志记得不好, 改都没地方改.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface LogInfo
{

    /*------ boot ------*/


    public function bootStartKeyStep(string $methodName) : string;

    public function bootEndKeyStep(string $methodName) : string;

    public function bootRegisterExistsProvider(string $id) : string;

    public function bootRegisterInvalidProvider(string $providerClass, string $validation = '') : string;

    public function bootRegisterProvider(string $id) : string;

    public function bootInvalidProviderConfiguration($index, $value) : string;

    public function bootMissBinding(string $abstract) : string;

    /*------ shell info ------*/

    public function shellReceiveInvalidRequest(string $message) : string;

    public function shellDirectiveNotExists(string $directiveId) : string;
}