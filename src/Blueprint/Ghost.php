<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Ghost\Cloner;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost
{
    public function getClone(string $cloneId) : Cloner;
}