<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Adapter;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Platform\Adapter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostAdapter extends Adapter
{

    /**
     * @return GhostRequest
     */
    public function getRequest() : GhostRequest;

    /**
     * @param GhostResponse
     * @return void
     */
    public function sendResponse($response) : void;

}