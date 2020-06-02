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
class BadNavigateCallException extends CommuneLogicException
{
    public function __construct(string $ucl, string $error)
    {
        $message = "bad navigate call by $ucl, $error";
        parent::__construct($message);
    }

}