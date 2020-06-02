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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CommuneErrorCode
{
    const SUCCESS = 0;
    const NO_CONTENT = 204;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const HANDLER_NOT_FOUND = 404;

    const HOST_RUNTIME_ERROR = 500;
    const HOST_REQUEST_FAIL = 501;
    const HOST_SESSION_FAIL = 502;

    const HOST_LOGIC_ERROR = 600;

    const DEFAULT_ERROR_MESSAGES = [
        0 => 'SUCCESS',
        204 => 'NO_CONTENT',
        400 => 'BAD_REQUEST',
        401 => 'UNAUTHORIZED',
        403 => 'FORBIDDEN',
        404 => 'HANDLER_NOT_FOUND',
        500 => 'RUNTIME_ERROR',
        501 => 'REQUEST_FAIL',
        502 => 'SESSION_FAIL',
        600 => 'LOGIC_ERROR',
    ];


}