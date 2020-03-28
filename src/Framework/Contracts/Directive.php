<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;

use Commune\Framework\Blueprint\Application;
use Commune\Message\Blueprint\Directive\DirectiveMsg;


/**
 * App 可以执行的命令. 通常通过 ID 匹配, 并执行.
 *
 * @see DirectiveMsg
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Directive
{
    public static function getId() : string;

    public function __invoke(Application $app, DirectiveMsg $msg);

}