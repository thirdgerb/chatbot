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

use Commune\Framework\Blueprint\AppKernel;
use Commune\Ghost\Contracts\GhostRequest;
use Commune\Ghost\Contracts\GhostResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostKernel extends AppKernel
{

    public function onSync(
        GhostRequest $request,
        GhostResponse $response
    ): void;

    public function onAsync() : bool;
}