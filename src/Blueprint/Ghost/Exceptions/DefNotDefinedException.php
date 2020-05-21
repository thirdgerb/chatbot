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

use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;

/**
 * 如果 Def 没有发现, 重置对话.
 * 有偶发的可能性.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DefNotDefinedException extends BrokenSessionException
{
    public function __construct(string $defType, string $defName)
    {
        $message = "definition not found, type $defType, name $defName";
        parent::__construct($message);
    }
}