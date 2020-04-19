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
 * 应用的启动流程.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Bootstrapper
{

    public function bootstrap() : void;
}