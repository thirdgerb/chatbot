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
    // success
    const SUCCESS = 0;



    // request failure
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const HANDLER_NOT_FOUND = 404;
    const REQUEST_TOO_LARGE = 413;
    const UNPROCESSABLE_ENTITY = 422;
    const LOCKED = 423;
    

    // runtime failure
    const HOST_RUNTIME_ERROR = 500;
    const HOST_REQUEST_FAIL = 501;
    const HOST_SESSION_FAIL = 502;

    // logic failure
    const HOST_LOGIC_ERROR = 600;

    const FAILURE_CODE_START = 400;

    const DEFAULT_ERROR_MESSAGES = [
        0 => 'SUCCESS',
        400 => 'BAD_REQUEST',
        401 => 'UNAUTHORIZED',
        403 => 'FORBIDDEN',
        404 => 'HANDLER_NOT_FOUND',
        413 => 'REQUEST_TOO_LARGE',
        422 => 'UNPROCESSABLE_ENTITY',
        423 => 'LOCKED',
        500 => 'RUNTIME_ERROR',
        501 => 'REQUEST_FAIL',
        502 => 'SESSION_FAIL',
        600 => 'LOGIC_ERROR',
    ];


}