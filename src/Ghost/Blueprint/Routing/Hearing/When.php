<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Routing\Hearing;

use Commune\Ghost\Blueprint\Routing\Hearing;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface When
{

    public function when(callable $rules) : Hearing;

}