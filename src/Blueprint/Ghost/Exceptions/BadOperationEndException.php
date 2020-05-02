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

use Commune\Blueprint\Exceptions\HostLogicException;


/**
 * 定义了无法正常结束的 Operator 流程.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BadOperationEndException extends HostLogicException
{
}