<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Exceptions;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OptionNotFoundException extends \RuntimeException
{
    public function __construct(string $method, string $optionId)
    {
        $message = "option not found that id is $optionId, called by $method";
        parent::__construct($message);
    }

}