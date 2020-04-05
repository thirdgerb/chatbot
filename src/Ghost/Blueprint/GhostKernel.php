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
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostKernel extends AppKernel
{

    public function onSync(
        GhtRequest $request,
        GhtResponse $response
    ): void;

    public function onAsync() : bool;
}