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

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\AppKernel;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Shell extends App, AppKernel
{
    /**
     * @return ShellConfig
     */
    public function getConfig() : ShellConfig;

}