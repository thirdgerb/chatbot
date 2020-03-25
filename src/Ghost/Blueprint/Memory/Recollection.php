<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Memory;

use ArrayAccess;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recollection extends ArrayAccess, ArrayAndJsonAble
{
    public function getId() : string;

}