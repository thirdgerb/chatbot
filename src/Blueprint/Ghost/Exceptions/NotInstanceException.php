<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Exceptions;

use Commune\Blueprint\Exceptions\HostLogicException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NotInstanceException extends HostLogicException
{
    public function __construct(string $class, string $method)
    {
        $message = "class should be instanced first, class: $class, method: $method";

        parent::__construct($message);
    }
}