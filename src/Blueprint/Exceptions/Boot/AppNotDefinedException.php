<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Exceptions\Boot;

use Commune\Blueprint\Exceptions\CommuneBootingException;

/**
 * 应用未定义
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AppNotDefinedException extends CommuneBootingException
{
    public function __construct(string $method, string $appName)
    {
        $message = "app not defined, appName: $appName, method: $method";
        parent::__construct($message);
    }

}