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
use Throwable;


/**
 * 机器人未运行.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CommuneNotRunningException extends CommuneBootingException
{
    public function __construct(string $method)
    {
        parent::__construct("host not running, called by $method");
    }

}