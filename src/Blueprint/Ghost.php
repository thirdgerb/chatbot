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
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Protocals\Intercom\GhostInput;

/**
 * Host 的灵魂. 对话机器人的内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost extends App
{

    /**
     * @return GhostConfig
     */
    public function getConfig() : GhostConfig;

    public function newCloner(GhostInput $input) : Cloner;

    public function handle(GhostRequest $request) : GhostResponse;
}