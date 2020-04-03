<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Exceptions;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SerializeSessionException extends AppLogicException
{

    public function __construct(string $sessionClass)
    {
        parent::__construct("forbid to serialize session $sessionClass");
    }

}