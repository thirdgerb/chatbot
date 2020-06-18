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
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Kernel\AppKernel;
use Commune\Blueprint\Shell\ShellSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Shell extends App, AppKernel
{
    /**
     * @return ShellConfig
     */
    public function getConfig() : ShellConfig;

    /**
     * @param ReqContainer $container
     * @param string $sessionId
     * @return ShellSession
     */
    public function newSession(ReqContainer $container, string $sessionId) : ShellSession;

}