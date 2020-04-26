<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Exceptions\Logic;

use Commune\Blueprint\Exceptions\HostLogicException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class InvalidArgumentException extends HostLogicException
{
    public function __construct(string $method, string $argName, string $error)
    {
        $message = "invalid argument for $method arg $argName, $error";
        parent::__construct($message);
    }

}