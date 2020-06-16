<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Exceptions\IO;

use Commune\Blueprint\Exceptions\Runtime\BrokenConversationException;
use Throwable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class InvalidSavedDataException extends BrokenConversationException
{
    public function __construct(
        string $error,
        Throwable $previous = null)
    {
        $message = "saved data invalid, error: $error";
        parent::__construct($message, 0, $previous);
    }

}