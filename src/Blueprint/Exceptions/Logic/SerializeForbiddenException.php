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

use Commune\Blueprint\Exceptions\HostLogicException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SerializeForbiddenException extends HostLogicException
{

    public function __construct(string $sessionClass)
    {
        parent::__construct("forbid to serialize $sessionClass");
    }

}