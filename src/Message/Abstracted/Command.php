<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Command extends ArrayAndJsonAble
{
    public function setCommandStr(string $commandStr) : void;

    public function getCommandStr() : ? string;

}