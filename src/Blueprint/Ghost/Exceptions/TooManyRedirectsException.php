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

use Commune\Blueprint\Exceptions\Runtime\BrokenConversationException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TooManyRedirectsException extends BrokenConversationException
{
    public function __construct(int $max)
    {
        $message = "too many dialog redirection, max is $max";
        parent::__construct($message);
    }

}