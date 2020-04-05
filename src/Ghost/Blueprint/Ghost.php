<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Framework\Blueprint\App;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost extends App
{
    public function getChatbotName() : string;

    public function getKernel() : GhostKernel;
}