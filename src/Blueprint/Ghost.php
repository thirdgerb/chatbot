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

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Kernel\AppKernel;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Protocals\Intercom\InputMsg;

/**
 * Host 的灵魂. 对话机器人的内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost extends App, AppKernel
{
    /**
     * @return GhostConfig
     */
    public function getConfig() : GhostConfig;

    /**
     * @param ReqContainer $container
     * @param InputMsg $input
     * @return Cloner
     */
    public function newCloner(ReqContainer $container, InputMsg $input) : Cloner;

    /**
     * @param GhostRequest $request
     * @return GhostResponse
     */
    public function handleRequest(GhostRequest $request) : GhostResponse;
}