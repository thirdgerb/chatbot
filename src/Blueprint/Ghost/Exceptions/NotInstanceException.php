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

use Commune\Blueprint\Exceptions\CommuneLogicException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NotInstanceException extends CommuneLogicException
{
    public function __construct(string $context)
    {
        $message = "class should be instanced first, context: $context";

        parent::__construct($message);
    }
}