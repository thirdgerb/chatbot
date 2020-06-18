<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Exceptions;

use Throwable;
use Commune\Blueprint\Kernel\Protocals\AppResponse;

/**
 * 机器人运行时异常.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CommuneRuntimeException extends \RuntimeException
{
    public function __construct(
        string $message = "",
        int $code = AppResponse::HOST_RUNTIME_ERROR,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}