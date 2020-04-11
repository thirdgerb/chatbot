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
class CategoryNotFoundException extends \LogicException
{
    public function __construct(string $categoryName = "")
    {
        $message = "category $categoryName not found";
        parent::__construct($message);
    }
}