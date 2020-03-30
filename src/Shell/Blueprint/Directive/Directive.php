<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Directive;

use Commune\Message\Blueprint\Directive\DirectiveMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Directive
{
    public static function getId() : string;

    public function invoke(DirectiveMsg $message) : void;
}