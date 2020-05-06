<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog\Routing\Hearing;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Dialog\Routing\MoveOn;
use Commune\Blueprint\Ghost\Dialog\Routing\Waiting;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Activate extends
    Dialog,
    MoveOn,
    Hearing,
    Waiting
{
    /**
     * 等待用户的回复.
     *
     * @param array $stageRoutes
     * @param array $contextRoutes
     * @param int|null $expire
     * @return Await
     */
    public function await(
        array $stageRoutes = [],
        array $contextRoutes = [],
        int $expire = null
    ) : Await;

}