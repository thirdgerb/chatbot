<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Session\GhtSession;

/**
 * 对话场景切换的状态. 
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextId 运行时所处的 ContextId
 * @property-read string $contextName 运行时所处的 ContextName
 * @property-read string $stageName 运行时所处的 Stage
 * @property-read string $stageEvent 运行时的 StageEvent
 */
interface Route
{
    public function depth() : int;

    public function fetchContext(GhtSession $session) : Context;

    /*------ 重定向 ------*/

    public function prev() : ? Route;

    public function root() : Route;

    public function prevContextRoute() : ? Route;

    public function prevStageRoute() : ? Route;

}