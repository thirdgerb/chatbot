<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\ContextDef;
use Commune\Blueprint\Ghost\Definition\StageDef;

/**
 * 对话场景切换的状态. 
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextId 运行时所处的 ContextId
 * @property-read string $contextName 运行时所处的 ContextName
 * @property-read string $stageName 运行时所处的 Stage
 */
interface Route
{
    public function depth() : int;

    /*------ 前进 ------*/

    public function forward(Node $node) : Route;

    /*------ Context ------*/

    public function findContext(Cloner $cloner) : Context;

    public function findContextDef(Cloner $cloner) : ContextDef;

    public function findStageDef(Cloner $cloner)  : StageDef;

    /*------ 重定向 ------*/

    public function prev() : ? Route;

    public function root() : Route;

    public function prevContextRoute() : ? Route;

    public function prevStageRoute() : ? Route;

    /*------ to string ------*/

    public function __toString() : string;
}