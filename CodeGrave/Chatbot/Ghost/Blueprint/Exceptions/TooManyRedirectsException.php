<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Exceptions;

use Commune\Chatbot\Blueprint\Exceptions\CloseSessionException;


/**
 * 产生了太多次重连.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TooManyRedirectsException extends CloseSessionException
{

}