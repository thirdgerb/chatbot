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
class InvalidQueryException extends BrokenConversationException
{

    public function __construct(string $contextName, string $key = null, string $error = null)
    {
        $message = "invalid context query of $contextName";
        isset($key) and $message .= ", key $key";
        isset($error) and $message .= " $error";

        parent::__construct($message);
    }

}