<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routing;

use Commune\Blueprint\Ghost\Operator\Await;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Awaiting
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